<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Booking;
use App\Models\BoatBooking;
use App\Mail\BookingConfirmedMail;

class PaymongoWebhookController extends Controller
{
    // Events we care about
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

    public function handle(Request $request)
    {
        $rawPayload = $request->getContent();
        $header     = $request->header('Paymongo-Signature', '');

        // -------------------------------------------------------
        // 1. Verify HMAC signature
        // -------------------------------------------------------
        if (!$this->verifySignature($rawPayload, $header)) {
            Log::warning('PayMongo webhook: signature verification failed', [
                'header' => $header,
                'ip'     => $request->ip(),
            ]);
            return response()->json(['message' => 'invalid signature'], 400);
        }

        // -------------------------------------------------------
        // 2. Decode event
        // -------------------------------------------------------
        $event = json_decode($rawPayload, true);
        $type  = data_get($event, 'data.attributes.type', '');

        Log::info('PayMongo webhook received', ['type' => $type]);

        // -------------------------------------------------------
        // 3. Route to handler
        // -------------------------------------------------------
        if (in_array($type, self::PAID_EVENTS)) {
            return $this->handlePaid($event, $type);
        }

        if (in_array($type, self::FAILED_EVENTS)) {
            return $this->handleFailed($event, $type);
        }

        // Acknowledge unknown events so PayMongo stops retrying
        return response()->json(['ok' => true, 'note' => 'event ignored']);
    }

    // -------------------------------------------------------
    // Payment PAID handler
    // -------------------------------------------------------
    private function handlePaid(array $event, string $type): \Illuminate\Http\JsonResponse
    {
        [$groupId, $paymentId, $amountPaid] = $this->extractPaymentData($event);

        if (!$groupId && !$paymentId) {
            Log::warning('PayMongo webhook paid: could not resolve group_id or payment_id', compact('type'));
            return response()->json(['ok' => true, 'note' => 'no identifiers found']);
        }

        try {
            DB::transaction(function () use ($groupId, $paymentId, $amountPaid) {

                $roomQuery = Booking::query();
                $boatQuery = BoatBooking::query();

                if ($groupId) {
                    $roomQuery->where('group_id', $groupId);
                    $boatQuery->where('group_id', $groupId);
                } else {
                    $roomQuery->where('payment_id', $paymentId);
                    $boatQuery->where('payment_id', $paymentId);
                }

                // Update both tables atomically
                $roomQuery->update([
                    'payment_status' => 'paid',
                    'status'         => 'confirmed',
                    'paid_at'        => now(),
                    'paid_amount'    => $amountPaid > 0 ? $amountPaid : DB::raw('deposit_amount'),
                    'expires_at'     => null, // clear the hold expiry — booking is now confirmed
                ]);

                $boatQuery->update([
                    'payment_status' => 'paid',
                    'status'         => 'confirmed',
                    'paid_at'        => now(),
                    'paid_amount'    => $amountPaid > 0 ? $amountPaid : DB::raw('total_amount'),
                ]);
            });

            Log::info('PayMongo webhook: bookings confirmed', [
                'group_id'   => $groupId,
                'payment_id' => $paymentId,
                'amount_php' => $amountPaid,
            ]);

        } catch (\Throwable $e) {
            Log::error('PayMongo webhook: DB update failed', [
                'error'      => $e->getMessage(),
                'group_id'   => $groupId,
                'payment_id' => $paymentId,
            ]);
            // Return 500 so PayMongo retries
            return response()->json(['message' => 'db error'], 500);
        }

        // -------------------------------------------------------
        // Send BookingConfirmedMail (non-critical — don't fail webhook)
        // -------------------------------------------------------
        if ($groupId) {
            $this->sendConfirmationMail($groupId, $amountPaid);
        }

        return response()->json(['ok' => true]);
    }

    // -------------------------------------------------------
    // Payment FAILED handler
    // -------------------------------------------------------
    private function handleFailed(array $event, string $type): \Illuminate\Http\JsonResponse
    {
        [$groupId, $paymentId] = $this->extractPaymentData($event);

        if (!$groupId && !$paymentId) {
            return response()->json(['ok' => true, 'note' => 'no identifiers found']);
        }

        try {
            $roomQuery = Booking::query();
            $boatQuery = BoatBooking::query();

            if ($groupId) {
                $roomQuery->where('group_id', $groupId);
                $boatQuery->where('group_id', $groupId);
            } else {
                $roomQuery->where('payment_id', $paymentId);
                $boatQuery->where('payment_id', $paymentId);
            }

            $roomQuery->update(['payment_status' => 'failed']);
            $boatQuery->update(['payment_status' => 'failed']);

            Log::info('PayMongo webhook: payment failed', [
                'group_id'   => $groupId,
                'payment_id' => $paymentId,
            ]);

        } catch (\Throwable $e) {
            Log::error('PayMongo webhook: failed-update DB error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'db error'], 500);
        }

        return response()->json(['ok' => true]);
    }

    // -------------------------------------------------------
    // Extract group_id, payment_id, amount from event payload
    // -------------------------------------------------------
    private function extractPaymentData(array $event): array
    {
        // PayMongo wraps the actual resource inside data.attributes.data
        $resource = data_get($event, 'data.attributes.data', data_get($event, 'data', []));
        $metadata = data_get($resource, 'attributes.metadata', []);

        // group_id stored in metadata when we created the link
        $groupId = $metadata['group_id'] ?? null;

        // Resolve payment_id: prefer nested pay_xxx from payments array
        $paymentId = data_get($resource, 'attributes.payments.data.0.id')
            ?? data_get($resource, 'id');

        // If payment_id doesn't start with pay_, try to look up by link id
        if ($paymentId && !str_starts_with((string) $paymentId, 'pay_')) {
            // It's a link id — find the booking by payment_id (we stored link id there)
            $booking = Booking::where('payment_id', $paymentId)->first()
                ?? BoatBooking::where('payment_id', $paymentId)->first();

            if ($booking && $booking->group_id) {
                $groupId = $booking->group_id;
            }
        }

        // If still no group_id, try looking up by payment_id
        if (!$groupId && $paymentId) {
            $booking = Booking::where('payment_id', $paymentId)->first()
                ?? BoatBooking::where('payment_id', $paymentId)->first();

            $groupId = $booking?->group_id;
        }

        // Amount paid in PHP (PayMongo sends centavos)
        $amountCentavos = (int) data_get($resource, 'attributes.amount', 0)
            ?: (int) data_get($resource, 'attributes.payments.data.0.attributes.amount', 0);

        $amountPhp = $amountCentavos > 0 ? round($amountCentavos / 100, 2) : 0.0;

        return [$groupId, $paymentId, $amountPhp];
    }

    // -------------------------------------------------------
    // HMAC-SHA256 signature verification
    // -------------------------------------------------------
    private function verifySignature(string $payload, string $header): bool
    {
        $secret = config('services.paymongo.webhook_secret');

        if (empty($secret)) {
            Log::error('PayMongo webhook: PAYMONGO_WEBHOOK_SECRET not set in .env');
            return false;
        }

        // Header format: t=<timestamp>,te=<test_sig>,li=<live_sig>
        $parts = [];
        foreach (explode(',', $header) as $part) {
            $segments = explode('=', $part, 2);
            if (count($segments) === 2) {
                $parts[trim($segments[0])] = trim($segments[1]);
            }
        }

        $timestamp = $parts['t'] ?? null;
        // 'te' = test mode signature, 'li' = live mode signature
        $signature = $parts['li'] ?? $parts['te'] ?? null;

        if (!$timestamp || !$signature) {
            Log::warning('PayMongo webhook: missing t or signature in header', ['header' => $header]);
            return false;
        }

        $computed = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        return hash_equals($computed, $signature);
    }

    // -------------------------------------------------------
    // Send BookingConfirmedMail after successful payment
    // -------------------------------------------------------
    private function sendConfirmationMail(string $groupId, float $amountPaid): void
    {
        try {
            $roomBookings = Booking::with('room')
                ->where('group_id', $groupId)
                ->where('payment_status', 'paid')
                ->get();

            $boatBookings = BoatBooking::with('boat')
                ->where('group_id', $groupId)
                ->where('payment_status', 'paid')
                ->get();

            $allBookings = $roomBookings->concat($boatBookings);
            $first       = $allBookings->first();

            if (!$first || empty($first->email)) return;

            $totalPaid = $amountPaid > 0
                ? $amountPaid
                : ($allBookings->sum('paid_amount') ?: $allBookings->sum('deposit_amount'));

            Mail::to($first->email)->send(new BookingConfirmedMail(
                bookings:  $allBookings->all(),
                totalPaid: (float) $totalPaid,
                groupId:   $groupId,
            ));

            Log::info('PayMongo webhook: BookingConfirmedMail sent', [
                'group_id' => $groupId,
                'email'    => $first->email,
            ]);

        } catch (\Throwable $e) {
            Log::error('PayMongo webhook: BookingConfirmedMail failed', [
                'group_id' => $groupId,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
