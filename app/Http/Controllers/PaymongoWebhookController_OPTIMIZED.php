<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Models\BoatBooking;
use App\Services\BookingPaymentFinalizer;
use Illuminate\Support\Facades\Queue;

/**
 * PayMongo Webhook Handler - Production Ready
 * 
 * CRITICAL REQUIREMENTS TO PREVENT WEBHOOK DISABLE:
 * 1. ALWAYS return HTTP 200 OK (never 4xx or 5xx)
 * 2. Respond within 5 seconds (PayMongo timeout)
 * 3. Handle ALL event types gracefully
 * 4. Never throw unhandled exceptions
 * 5. Log everything for debugging
 */
class PaymongoWebhookController extends Controller
{
    private const TIMEOUT_SECONDS = 3; // Respond within 3s to be safe
    
    public function __construct(private BookingPaymentFinalizer $payments)
    {
        // Set max execution time for this controller
        set_time_limit(self::TIMEOUT_SECONDS);
    }

    // Event types that mean "money received"
    const PAID_EVENTS = [
        'payment.paid',
        'link.payment.paid',
        'checkout_session.payment.paid',
    ];

    const FAILED_EVENTS = [
        'payment.failed',
        'link.payment.failed',
        'checkout_session.payment.failed',
    ];

    /**
     * Main webhook entry point
     * 
     * CRITICAL: This method MUST ALWAYS return 200 OK
     * PayMongo will disable webhook if it receives 4xx/5xx responses
     */
    public function handle(Request $request): \Illuminate\Http\JsonResponse
    {
        $startTime = microtime(true);
        
        try {
            // STEP 1: Log incoming request
            Log::info('[Webhook] ===== PayMongo webhook received =====', [
                'ip'      => $request->ip(),
                'method'  => $request->method(),
                'url'     => $request->fullUrl(),
                'headers' => $request->headers->all(),
            ]);

            // STEP 2: Get raw payload (CRITICAL: must use getContent() not input())
            $rawPayload = $request->getContent();
            $header     = $request->header('Paymongo-Signature', '');

            if (empty($rawPayload)) {
                Log::warning('[Webhook] Empty payload received');
                return $this->successResponse('empty payload');
            }

            Log::info('[Webhook] Payload received', [
                'signature_header' => $header,
                'payload_length'   => strlen($rawPayload),
                'payload_preview'  => substr($rawPayload, 0, 500),
            ]);

            // STEP 3: Verify signature (but ALWAYS return 200 OK even if fails)
            if (!$this->verifySignature($rawPayload, $header)) {
                Log::warning('[Webhook] Signature verification FAILED — acknowledging anyway', [
                    'header' => $header,
                    'ip'     => $request->ip(),
                ]);
                return $this->successResponse('signature verification failed');
            }

            Log::info('[Webhook] Signature verified OK');

            // STEP 4: Parse JSON (but ALWAYS return 200 OK even if fails)
            $event = json_decode($rawPayload, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('[Webhook] JSON decode failed', [
                    'error' => json_last_error_msg(),
                    'payload_preview' => substr($rawPayload, 0, 200),
                ]);
                return $this->successResponse('json decode failed');
            }

            // STEP 5: Extract event type
            $type = data_get($event, 'data.attributes.type', '');

            Log::info('[Webhook] Event type extracted', [
                'type'     => $type,
                'event_id' => data_get($event, 'data.id'),
            ]);

            // STEP 6: Route to handler (all handlers MUST return 200 OK)
            if (in_array($type, self::PAID_EVENTS)) {
                return $this->handlePaidAsync($event, $type, $startTime);
            }

            if (in_array($type, self::FAILED_EVENTS)) {
                return $this->handleFailedAsync($event, $type, $startTime);
            }

            // Unknown event type - acknowledge to prevent retries
            Log::info('[Webhook] Event type not handled — acknowledging', ['type' => $type]);
            return $this->successResponse('event ignored');

        } catch (\Throwable $e) {
            // CRITICAL: Catch ALL exceptions and return 200 OK
            Log::error('[Webhook] UNHANDLED EXCEPTION — acknowledging anyway to prevent webhook disable', [
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);
            
            return $this->successResponse('unhandled exception occurred');
        }
    }

    /**
     * Handle PAID events with async processing
     * Responds immediately, processes in background
     */
    private function handlePaidAsync(array $event, string $type, float $startTime): \Illuminate\Http\JsonResponse
    {
        Log::info('[Webhook] Handling PAID event', ['type' => $type]);

        try {
            // Extract identifiers (fast operation)
            [$groupId, $sessionId, $paymentId, $amountPaid] = $this->extractAllIdentifiers($event, $type);

            Log::info('[Webhook] Identifiers extracted', [
                'group_id'   => $groupId,
                'session_id' => $sessionId,
                'payment_id' => $paymentId,
                'amount_php' => $amountPaid,
            ]);

            // Quick DB lookup if needed (indexed query, should be fast)
            if (!$groupId) {
                $groupId = $this->resolveGroupId($sessionId, $paymentId);
            }

            if (!$groupId && !$paymentId && !$sessionId) {
                Log::error('[Webhook] No identifiers found');
                return $this->successResponse('no identifiers found');
            }

            // Check if we're approaching timeout
            $elapsed = microtime(true) - $startTime;
            if ($elapsed > 2.0) {
                // Queue for background processing
                Log::warning('[Webhook] Approaching timeout, queueing for background processing', [
                    'elapsed' => $elapsed,
                ]);
                
                Queue::push(function() use ($groupId, $sessionId, $paymentId, $amountPaid) {
                    $this->processPayment($groupId, $sessionId, $paymentId, $amountPaid);
                });
                
                return $this->successResponse('queued for processing');
            }

            // Process immediately if we have time
            $this->processPayment($groupId, $sessionId, $paymentId, $amountPaid);

            Log::info('[Webhook] PAID event handled successfully', [
                'group_id' => $groupId,
                'elapsed'  => microtime(true) - $startTime,
            ]);

            return $this->successResponse();

        } catch (\Throwable $e) {
            Log::error('[Webhook] PAID event error — acknowledging anyway', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->successResponse('paid event error occurred');
        }
    }

    /**
     * Handle FAILED events with async processing
     */
    private function handleFailedAsync(array $event, string $type, float $startTime): \Illuminate\Http\JsonResponse
    {
        Log::info('[Webhook] Handling FAILED event', ['type' => $type]);

        try {
            [$groupId, $sessionId, $paymentId] = $this->extractAllIdentifiers($event, $type);

            if (!$groupId) {
                $groupId = $this->resolveGroupId($sessionId, $paymentId);
            }

            if (!$groupId && !$sessionId && !$paymentId) {
                Log::warning('[Webhook] FAILED event: no identifiers found');
                return $this->successResponse('no identifiers found');
            }

            // Mark as failed (fast operation)
            $result = $this->payments->markFailed($groupId, $sessionId, $paymentId);

            Log::info('[Webhook] FAILED event handled', [
                'result'  => $result,
                'elapsed' => microtime(true) - $startTime,
            ]);

            return $this->successResponse();

        } catch (\Throwable $e) {
            Log::error('[Webhook] FAILED event error — acknowledging anyway', [
                'error' => $e->getMessage(),
            ]);
            return $this->successResponse('failed event error occurred');
        }
    }

    /**
     * Process payment (can be called sync or async)
     */
    private function processPayment(?string $groupId, ?string $sessionId, ?string $paymentId, float $amountPaid): void
    {
        try {
            $result = $this->payments->markPaid($groupId, $sessionId, $paymentId, $amountPaid);
            $groupId = $result['group_id'] ?? $groupId;

            Log::info('[Webhook] Payment processed', $result);

            // Send email if newly paid (queued, non-blocking)
            if ($groupId && (($result['newly_paid_count'] ?? 0) > 0)) {
                $this->payments->sendConfirmationMail($groupId);
            }

        } catch (\Throwable $e) {
            Log::error('[Webhook] Payment processing failed', [
                'error'      => $e->getMessage(),
                'group_id'   => $groupId,
                'session_id' => $sessionId,
            ]);
        }
    }

    /**
     * Extract all identifiers from PayMongo payload
     */
    private function extractAllIdentifiers(array $event, string $type): array
    {
        $resource = data_get($event, 'data.attributes.data', data_get($event, 'data', []));

        Log::info('[Webhook] Resource extracted', [
            'resource_id'   => data_get($resource, 'id'),
            'resource_type' => data_get($resource, 'type'),
        ]);

        // Extract group_id from metadata
        $metadata = data_get($resource, 'attributes.metadata', []);
        $groupId  = $metadata['group_id'] ?? null;

        // Extract session_id (cs_xxxx)
        $sessionId = null;
        $resourceId = data_get($resource, 'id', '');
        if (str_starts_with((string) $resourceId, 'cs_')) {
            $sessionId = $resourceId;
        }

        // Extract payment_id (pay_xxxx)
        $paymentId = data_get($resource, 'attributes.payments.0.id')
            ?: data_get($resource, 'attributes.payments.data.0.id')
            ?: data_get($resource, 'attributes.payment_intent.attributes.payments.0.id')
            ?: data_get($resource, 'attributes.payment_intent.attributes.payments.data.0.id');

        if (!$paymentId && str_starts_with((string) $resourceId, 'pay_')) {
            $paymentId = $resourceId;
        }

        // Extract amount
        $amountCentavos = (int) (
            data_get($resource, 'attributes.payments.0.attributes.amount')
            ?: data_get($resource, 'attributes.payments.data.0.attributes.amount')
            ?: data_get($resource, 'attributes.payment_intent.attributes.payments.0.attributes.amount')
            ?: data_get($resource, 'attributes.payment_intent.attributes.payments.data.0.attributes.amount')
            ?: data_get($resource, 'attributes.amount', 0)
        );

        $amountPhp = $amountCentavos > 0 ? round($amountCentavos / 100, 2) : 0.0;

        Log::info('[Webhook] Identifiers extracted', [
            'group_id'   => $groupId,
            'session_id' => $sessionId,
            'payment_id' => $paymentId,
            'amount_php' => $amountPhp,
        ]);

        return [$groupId, $sessionId, $paymentId, $amountPhp];
    }

    /**
     * Resolve group_id from database
     */
    private function resolveGroupId(?string $sessionId, ?string $paymentId): ?string
    {
        if ($sessionId) {
            $b = Booking::where('payment_id', $sessionId)->first()
              ?? BoatBooking::where('payment_id', $sessionId)->first();
            if ($b?->group_id) {
                Log::info('[Webhook] group_id resolved via session_id', [
                    'session_id' => $sessionId,
                    'group_id'   => $b->group_id,
                ]);
                return $b->group_id;
            }
        }

        if ($paymentId) {
            $b = Booking::where('payment_id', $paymentId)->first()
              ?? BoatBooking::where('payment_id', $paymentId)->first();
            if ($b?->group_id) {
                Log::info('[Webhook] group_id resolved via payment_id', [
                    'payment_id' => $paymentId,
                    'group_id'   => $b->group_id,
                ]);
                return $b->group_id;
            }
        }

        Log::warning('[Webhook] Could not resolve group_id', [
            'session_id' => $sessionId,
            'payment_id' => $paymentId,
        ]);

        return null;
    }

    /**
     * Verify HMAC-SHA256 signature
     */
    private function verifySignature(string $payload, string $header): bool
    {
        $secret = config('services.paymongo.webhook_secret');

        if (empty($secret)) {
            Log::error('[Webhook] PAYMONGO_WEBHOOK_SECRET not configured');
            return false;
        }

        if (empty($header)) {
            Log::warning('[Webhook] No signature header provided');
            return false;
        }

        // Parse header: t=<timestamp>,te=<test_sig>,li=<live_sig>
        $parts = [];
        foreach (explode(',', $header) as $part) {
            $segments = explode('=', $part, 2);
            if (count($segments) === 2) {
                $parts[trim($segments[0])] = trim($segments[1]);
            }
        }

        $timestamp = $parts['t']  ?? null;
        $signature = $parts['li'] ?? $parts['te'] ?? null;

        Log::info('[Webhook] Signature verification', [
            'has_timestamp' => !empty($timestamp),
            'has_signature' => !empty($signature),
            'mode'          => isset($parts['li']) ? 'live' : (isset($parts['te']) ? 'test' : 'unknown'),
        ]);

        if (!$timestamp || !$signature) {
            Log::warning('[Webhook] Missing timestamp or signature');
            return false;
        }

        // Verify signature
        $computed = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
        $match    = hash_equals($computed, $signature);

        if (!$match) {
            Log::warning('[Webhook] Signature mismatch', [
                'computed_prefix'  => substr($computed, 0, 8) . '...',
                'received_prefix'  => substr($signature, 0, 8) . '...',
            ]);
        }

        return $match;
    }

    /**
     * CRITICAL: Always return 200 OK with consistent format
     */
    private function successResponse(string $note = 'success'): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'ok'   => true,
            'note' => $note,
        ], 200);
    }
}
