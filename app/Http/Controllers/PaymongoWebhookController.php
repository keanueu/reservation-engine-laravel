<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Models\BoatBooking;
use App\Services\BookingPaymentFinalizer;

class PaymongoWebhookController extends Controller
{
    public function __construct(private BookingPaymentFinalizer $payments)
    {
    }

    // -------------------------------------------------------
    // PayMongo event types that mean "money received"
    // -------------------------------------------------------
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

    // -------------------------------------------------------
    // Entry point — called by PayMongo's server
    // -------------------------------------------------------
    public function handle(Request $request): \Illuminate\Http\JsonResponse
    {
        // STEP 1: Log that the webhook was hit
        Log::info('[Webhook] ===== PayMongo webhook received =====', [
            'ip'      => $request->ip(),
            'method'  => $request->method(),
            'url'     => $request->fullUrl(),
        ]);

        $rawPayload = $request->getContent();
        $header     = $request->header('Paymongo-Signature', '');

        // STEP 2: Log the raw payload and signature header for debugging
        Log::info('[Webhook] Raw payload received', [
            'signature_header' => $header,
            'payload_length'   => strlen($rawPayload),
            'payload_preview'  => substr($rawPayload, 0, 500),
        ]);

        // STEP 3: Verify HMAC signature
        // CRITICAL: Always return 200 OK to prevent PayMongo from disabling webhook
        if (!$this->verifySignature($rawPayload, $header)) {
            Log::warning('[Webhook] Signature verification FAILED — acknowledging anyway to prevent webhook disable', [
                'header' => $header,
                'ip'     => $request->ip(),
            ]);
            // Return 200 OK even on signature failure to prevent webhook disable
            return response()->json(['ok' => true, 'note' => 'signature verification failed']);
        }

        Log::info('[Webhook] Signature verified OK');

        // STEP 4: Decode and extract event type
        $event = json_decode($rawPayload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('[Webhook] JSON decode failed — acknowledging anyway', ['error' => json_last_error_msg()]);
            // Return 200 OK even on JSON error to prevent webhook disable
            return response()->json(['ok' => true, 'note' => 'json decode failed']);
        }

        $type = data_get($event, 'data.attributes.type', '');

        Log::info('[Webhook] Event type extracted', [
            'type'     => $type,
            'event_id' => data_get($event, 'data.id'),
        ]);

        // STEP 5: Route to the correct handler
        if (in_array($type, self::PAID_EVENTS)) {
            return $this->handlePaid($event, $type);
        }

        if (in_array($type, self::FAILED_EVENTS)) {
            return $this->handleFailed($event, $type);
        }

        Log::info('[Webhook] Event type not handled — acknowledging to stop retries', ['type' => $type]);
        return response()->json(['ok' => true, 'note' => 'event ignored']);
    }

    // -------------------------------------------------------
    // Handle payment PAID events
    // -------------------------------------------------------
    private function handlePaid(array $event, string $type): \Illuminate\Http\JsonResponse
    {
        Log::info('[Webhook] Handling PAID event', ['type' => $type]);

        // STEP 6: Extract all identifiers from the payload
        [$groupId, $sessionId, $paymentId, $amountPaid] = $this->extractAllIdentifiers($event, $type);

        Log::info('[Webhook] Identifiers extracted', [
            'group_id'   => $groupId,
            'session_id' => $sessionId,
            'payment_id' => $paymentId,
            'amount_php' => $amountPaid,
        ]);

        // STEP 7: Resolve group_id if not in metadata — fall back to DB lookup
        if (!$groupId) {
            $groupId = $this->resolveGroupId($sessionId, $paymentId);
            Log::info('[Webhook] group_id resolved from DB lookup', ['group_id' => $groupId]);
        }

        if (!$groupId && !$paymentId && !$sessionId) {
            Log::error('[Webhook] CRITICAL: Could not resolve any identifier — cannot update DB');
            return response()->json(['ok' => true, 'note' => 'no identifiers found']);
        }

        // STEP 8: Update the database atomically
        try {
            $result = $this->payments->markPaid($groupId, $sessionId, $paymentId, $amountPaid);
            $groupId = $result['group_id'] ?? $groupId;

            Log::info('[Webhook] DB update SUCCESS', $result);

        } catch (\Throwable $e) {
            Log::error('[Webhook] DB transaction FAILED — acknowledging anyway to prevent webhook disable', [
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
                'group_id'   => $groupId,
                'session_id' => $sessionId,
            ]);
            // CRITICAL: Return 200 OK even on DB error to prevent webhook disable
            // PayMongo will NOT retry, but webhook stays enabled
            return response()->json(['ok' => true, 'note' => 'db error occurred']);
        }

        // STEP 10: Send confirmation email (non-critical — never fail the webhook)
        if ($groupId && (($result['newly_paid_count'] ?? 0) > 0)) {
            $this->payments->sendConfirmationMail($groupId);
        }

        Log::info('[Webhook] ===== PAID event handled successfully =====', ['group_id' => $groupId]);
        return response()->json(['ok' => true]);
    }

    // -------------------------------------------------------
    // Handle payment FAILED events
    // -------------------------------------------------------
    private function handleFailed(array $event, string $type): \Illuminate\Http\JsonResponse
    {
        Log::info('[Webhook] Handling FAILED event', ['type' => $type]);

        [$groupId, $sessionId, $paymentId] = $this->extractAllIdentifiers($event, $type);

        if (!$groupId) {
            $groupId = $this->resolveGroupId($sessionId, $paymentId);
        }

        if (!$groupId && !$sessionId && !$paymentId) {
            Log::warning('[Webhook] FAILED event: no identifiers found');
            return response()->json(['ok' => true, 'note' => 'no identifiers found']);
        }

        try {
            $result = $this->payments->markFailed($groupId, $sessionId, $paymentId);

            Log::info('[Webhook] FAILED event DB update done', $result);

        } catch (\Throwable $e) {
            Log::error('[Webhook] FAILED event DB error — acknowledging anyway', ['error' => $e->getMessage()]);
            // CRITICAL: Return 200 OK even on error to prevent webhook disable
            return response()->json(['ok' => true, 'note' => 'db error occurred']);
        }

        return response()->json(['ok' => true]);
    }

    // -------------------------------------------------------
    // Extract ALL identifiers from the PayMongo payload.
    //
    // PayMongo checkout_session.payment.paid payload structure:
    //
    // data.attributes.type = "checkout_session.payment.paid"
    // data.attributes.data = { checkout session object }
    //   .id                = "cs_xxxx"  ← checkout session id
    //   .attributes.metadata.group_id  ← our group_id
    //   .attributes.payments.data[0].id = "pay_xxxx"  ← actual payment id
    //   .attributes.payments.data[0].attributes.amount = centavos
    // -------------------------------------------------------
    private function extractAllIdentifiers(array $event, string $type): array
    {
        // The checkout session / payment / link object lives here
        $resource = data_get($event, 'data.attributes.data', data_get($event, 'data', []));

        Log::info('[Webhook] Resource object keys', [
            'resource_id'   => data_get($resource, 'id'),
            'resource_type' => data_get($resource, 'type'),
        ]);

        // --- group_id from metadata ---
        $metadata = data_get($resource, 'attributes.metadata', []);
        $groupId  = $metadata['group_id'] ?? null;

        Log::info('[Webhook] Metadata extracted', [
            'metadata' => $metadata,
            'group_id' => $groupId,
        ]);

        // --- Checkout session id (cs_xxxx) ---
        $sessionId = null;
        $resourceId = data_get($resource, 'id', '');
        if (str_starts_with((string) $resourceId, 'cs_')) {
            $sessionId = $resourceId;
        }

        // --- Payment id (pay_xxxx) from nested payments array ---
        $paymentId = data_get($resource, 'attributes.payments.0.id')
            ?: data_get($resource, 'attributes.payments.data.0.id')
            ?: data_get($resource, 'attributes.payment_intent.attributes.payments.0.id')
            ?: data_get($resource, 'attributes.payment_intent.attributes.payments.data.0.id');

        // If resource itself is a payment (payment.paid event)
        if (!$paymentId && str_starts_with((string) $resourceId, 'pay_')) {
            $paymentId = $resourceId;
        }

        // --- Amount in PHP ---
        $amountCentavos = (int) (
            data_get($resource, 'attributes.payments.0.attributes.amount')
            ?: data_get($resource, 'attributes.payments.data.0.attributes.amount')
            ?: data_get($resource, 'attributes.payment_intent.attributes.payments.0.attributes.amount')
            ?: data_get($resource, 'attributes.payment_intent.attributes.payments.data.0.attributes.amount')
            ?: data_get($resource, 'attributes.amount', 0)
        );

        $amountPhp = $amountCentavos > 0 ? round($amountCentavos / 100, 2) : 0.0;

        Log::info('[Webhook] Identifiers from payload', [
            'group_id'   => $groupId,
            'session_id' => $sessionId,
            'payment_id' => $paymentId,
            'amount_php' => $amountPhp,
        ]);

        return [$groupId, $sessionId, $paymentId, $amountPhp];
    }

    // -------------------------------------------------------
    // Resolve group_id from DB when metadata lookup fails
    // -------------------------------------------------------
    private function resolveGroupId(?string $sessionId, ?string $paymentId): ?string
    {
        // Try checkout session id first (stored as payment_id when we created the session)
        if ($sessionId) {
            $b = Booking::where('payment_id', $sessionId)->first()
              ?? BoatBooking::where('payment_id', $sessionId)->first();
            if ($b?->group_id) {
                Log::info('[Webhook] group_id resolved via session_id DB lookup', [
                    'session_id' => $sessionId,
                    'group_id'   => $b->group_id,
                ]);
                return $b->group_id;
            }
        }

        // Try payment id
        if ($paymentId) {
            $b = Booking::where('payment_id', $paymentId)->first()
              ?? BoatBooking::where('payment_id', $paymentId)->first();
            if ($b?->group_id) {
                Log::info('[Webhook] group_id resolved via payment_id DB lookup', [
                    'payment_id' => $paymentId,
                    'group_id'   => $b->group_id,
                ]);
                return $b->group_id;
            }
        }

        Log::warning('[Webhook] Could not resolve group_id from DB', [
            'session_id' => $sessionId,
            'payment_id' => $paymentId,
        ]);

        return null;
    }

    // -------------------------------------------------------
    // HMAC-SHA256 signature verification
    // PayMongo header format: t=<timestamp>,te=<test_sig>,li=<live_sig>
    // -------------------------------------------------------
    private function verifySignature(string $payload, string $header): bool
    {
        $secret = config('services.paymongo.webhook_secret');

        if (empty($secret)) {
            Log::error('[Webhook] PAYMONGO_WEBHOOK_SECRET is not set in .env — cannot verify signature');
            return false;
        }

        $parts = [];
        foreach (explode(',', $header) as $part) {
            $segments = explode('=', $part, 2);
            if (count($segments) === 2) {
                $parts[trim($segments[0])] = trim($segments[1]);
            }
        }

        $timestamp = $parts['t']  ?? null;
        $signature = $parts['li'] ?? $parts['te'] ?? null; // li=live, te=test

        Log::info('[Webhook] Signature parts', [
            'timestamp'      => $timestamp,
            'has_signature'  => !empty($signature),
            'mode'           => isset($parts['li']) ? 'live' : (isset($parts['te']) ? 'test' : 'unknown'),
        ]);

        if (!$timestamp || !$signature) {
            Log::warning('[Webhook] Missing timestamp or signature in header', ['header' => $header]);
            return false;
        }

        $computed = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
        $match    = hash_equals($computed, $signature);

        if (!$match) {
            Log::warning('[Webhook] HMAC mismatch', [
                'computed_prefix'  => substr($computed, 0, 8) . '...',
                'received_prefix'  => substr($signature, 0, 8) . '...',
            ]);
        }

        return $match;
    }

}
