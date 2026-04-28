<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Models\Booking;
use App\Models\BoatBooking;
use App\Notifications\BookingReceiptNotification;

class PaymongoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $header = $request->header('Paymongo-Signature', '');

        // --- Signature validation ---
        $pairs = [];
        foreach (explode(',', $header) as $part) {
            [$k, $v] = array_map('trim', explode('=', $part) + [1 => null]);
            $pairs[$k] = $v;
        }

        $timestamp = $pairs['t'] ?? null;
        $sig = $pairs['te'] ?? $pairs['li'] ?? null;
        $secret = config('services.paymongo.webhook_secret');

        if (!$timestamp || !$sig || !$secret) {
            Log::warning('Paymongo webhook missing signature or secret.');
            return response()->json(['message' => 'invalid signature'], 400);
        }

        $toSign = $timestamp . '.' . $payload;
        $computed = hash_hmac('sha256', $toSign, $secret);

        if (!hash_equals($computed, $sig)) {
            Log::warning('Paymongo webhook signature mismatch');
            return response()->json(['message' => 'invalid signature'], 400);
        }

        // --- Decode payload ---
        $event = json_decode($payload, true);
        $type = data_get($event, 'data.attributes.type');
        Log::info('Paymongo verified event: ' . $type, $event);

        // --- Process payment status changes ---
        if (
            in_array($type, [
                'payment.paid',
                'link.payment.paid',
                'checkout_session.payment.paid',
                'payment.failed',
                'link.payment.failed',
                'checkout_session.payment.failed'
            ])
        ) {
            $data = data_get($event, 'data.attributes.data', data_get($event, 'data'));
            $metadata = data_get($data, 'attributes.metadata', []);

            $groupId = $metadata['group_id'] ?? null;
            $bookingId = $metadata['booking_id'] ?? null;

            $paymongoId = data_get($data, 'id') ?? data_get($data, 'attributes.id');
            $nestedPayId = data_get($data, 'attributes.payments.data.0.id');
            if ($nestedPayId && str_starts_with($nestedPayId, 'pay_')) {
                $paymongoId = $nestedPayId;
            }

            // --- Find booking ---
            $booking = null;
            if ($bookingId) {
                $booking = Booking::find($bookingId);
            } else {
                $booking = Booking::where('payment_id', $paymongoId)->first();
            }

            if ($booking) {
                $groupId = $booking->group_id ?? null;
                $roomQuery = Booking::query();
                $boatQuery = BoatBooking::query();

                if ($groupId) {
                    $roomQuery->where('group_id', $groupId);
                    $boatQuery->where('group_id', $groupId);
                } else {
                    $roomQuery->where('id', $booking->id);
                    $boatQuery->where('payment_id', $paymongoId);
                }

                if (in_array($type, ['payment.paid', 'link.payment.paid', 'checkout_session.payment.paid'])) {
                    $roomQuery->update(['payment_status' => 'paid', 'paid_at' => now()]);
                    $boatQuery->update(['payment_status' => 'paid', 'paid_at' => now()]);

                    Log::info("Payment PAID for group_id={$groupId}, updated room + boat bookings.");

                    // Send booking receipt emails to guest(s). Group bookings by recipient email so they
                    // receive a single receipt containing all their bookings in the group.
                    try {
                        $roomBookings = Booking::with('room')->where('group_id', $groupId)->get();
                        $boatBookings = BoatBooking::with('boat')->where('group_id', $groupId)->get();

                        $all = $roomBookings->concat($boatBookings);
                        $emails = $all->pluck('email')->unique()->filter();

                        foreach ($emails as $email) {
                            $items = $all->filter(function ($b) use ($email) {
                                return ($b->email ?? null) === $email;
                            })->values();

                            if ($items->isNotEmpty()) {
                                Notification::route('mail', $email)
                                    ->notify(new BookingReceiptNotification($items));
                            }
                        }
                    } catch (\Throwable $ex) {
                        Log::error('Failed to send booking receipt emails: ' . $ex->getMessage());
                    }
                } elseif (in_array($type, ['payment.failed', 'link.payment.failed', 'checkout_session.payment.failed'])) {
                    $roomQuery->update(['payment_status' => 'failed']);
                    $boatQuery->update(['payment_status' => 'failed']);

                    Log::info("Payment FAILED for group_id={$groupId}, updated room + boat bookings.");
                }
            }
        }

        return response()->json(['ok' => true]);
    }
}
