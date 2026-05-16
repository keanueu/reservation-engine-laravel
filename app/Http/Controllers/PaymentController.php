<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BoatBooking;
use App\Services\BookingPaymentFinalizer;
use App\Services\PaymongoService;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $paymongo;
    protected $payments;

    public function __construct(PaymongoService $paymongo, BookingPaymentFinalizer $payments)
    {
        $this->paymongo = $paymongo;
        $this->payments = $payments;
    }

    public function payForBooking(Request $request)
    {
        $groupId = $request->query('group_id');
        $routeBookingId = $request->route('booking');
        $amountPhp = (float) $request->query('amount', 0);

        $roomBookings = collect();
        $boatBookings = collect();

        if ($groupId) {
            $roomBookings = Booking::with('room')->where('group_id', $groupId)->get();
            $boatBookings = BoatBooking::with('boat')->where('group_id', $groupId)->get();
        } elseif ($routeBookingId) {
            $singleBooking = Booking::with('room')->find($routeBookingId);

            if ($singleBooking) {
                if (!$singleBooking->group_id) {
                    $singleBooking->group_id = Str::uuid()->toString();
                    $singleBooking->save();
                }

                $groupId = $singleBooking->group_id;
                $roomBookings = Booking::with('room')->where('group_id', $groupId)->get();
                $boatBookings = BoatBooking::with('boat')->where('group_id', $groupId)->get();
            }
        }

        // Use either a room booking or a boat booking as the main reference
        $booking = $roomBookings->first() ?: $boatBookings->first();

        if (!$booking) {
            return redirect()->back()->with('error', 'No booking data found for payment.');
        }

        // --- Compute total ---
        if (!$amountPhp) {
            $storedTotalToCharge = $roomBookings->sum('total_to_charge') + $boatBookings->sum('total_to_charge');

            if ($storedTotalToCharge > 0) {
                $amountPhp = $storedTotalToCharge;
            } else {
                $fullTotal = $roomBookings->sum('total_amount') + $boatBookings->sum('total_amount');
                $depositPercent = (float) Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
                $depositFeePercent = (float) Setting::get('deposit_fee_percentage', config('booking.deposit_fee_percentage', 0));
                $depositAmount = $fullTotal * ($depositPercent / 100);
                $amountPhp = $depositAmount + ($depositAmount * ($depositFeePercent / 100));
            }
        }

        $amount = max(10000, (int) round($amountPhp * 100));

        \Log::info('PayMongo Payment Debug (final)', [
            'group_id' => $groupId,
            'amountPhp' => $amountPhp,
            'amountCentavos' => $amount,
        ]);

        // --- Build description safely ---
        $roomNames = $roomBookings->map(fn($b) => optional($b->room)->room_name)->filter()->all();
        $boatNames = $boatBookings->map(fn($b) => optional($b->boat)->name)->filter()->all();

        $itemsDescription = [];
        if (!empty($roomNames))
            $itemsDescription[] = 'Rooms: ' . implode(', ', $roomNames);
        if (!empty($boatNames))
            $itemsDescription[] = 'Boats: ' . implode(', ', $boatNames);

        $fullDescription = implode(' | ', $itemsDescription);

        // --- Build metadata ---
        $metadata = [
            'description'    => "Booking Group {$groupId} - {$fullDescription}",
            'customer_name'  => $booking->name  ?? 'Unknown',
            'customer_email' => $booking->email ?? 'Unknown',
            'group_id'       => $groupId,
        ];

        // --- Redirect URLs ---
        $successUrl = $this->returnUrl(
            config('services.paymongo.success_url'),
            route('booking.success', ['group_id' => $groupId]),
            ['group_id' => $groupId]
        );
        $cancelUrl = $this->returnUrl(
            config('services.paymongo.cancel_url'),
            route('payment.cancel', ['group_id' => $groupId]),
            ['group_id' => $groupId]
        );

        // --- Create PayMongo Checkout Session (supports native success/cancel redirect) ---
        $response = $this->paymongo->createCheckoutSession(
            amountInCents: $amount,
            description:   $fullDescription ?: 'Cabanas Beach Resort Booking',
            metadata:      $metadata,
            successUrl:    $successUrl,
            cancelUrl:     $cancelUrl,
        );

        $success     = $response['success']      ?? false;
        $sessionId   = $response['session_id']   ?? null;
        $checkoutUrl = $response['checkout_url'] ?? null;

        if (!$success) {
            Log::error('PayMongo createCheckoutSession failed', ['group_id' => $groupId, 'resp' => $response]);
        }

        // --- Save session id as payment_id for webhook lookup ---
        if ($groupId && $sessionId) {
            Booking::where('group_id', $groupId)->update(['payment_id' => $sessionId]);
            BoatBooking::where('group_id', $groupId)->update(['payment_id' => $sessionId]);

            session([
                'pending_booking_group' => $groupId,
                'pending_booking_ids' => $roomBookings->concat($boatBookings)->pluck('id')->all(),
            ]);
        }

        // --- Redirect to PayMongo Checkout ---
        if ($checkoutUrl) {
            return redirect()->away($checkoutUrl);
        }

        $errorMsg = 'Unable to create payment link.';
        if (!empty($response['message'])) {
            $errorMsg .= ' ' . (is_array($response['message']) ? json_encode($response['message']) : $response['message']);
        } elseif (!empty($response['raw']['errors'])) {
            $errorMsg .= ' ' . json_encode($response['raw']['errors']);
        }

        return redirect()->back()->with('error', $errorMsg);
    }

    public function bookingSuccess(Request $request)
    {
        $groupId = $request->query('group_id', session('pending_booking_group'));
        $extensionId = $request->query('extension_id');

        if ($groupId) {
            $this->syncCheckoutSessionPayment($groupId);
        }

        if ($extensionId) {
            app(BookingExtensionController::class)->refresh($request, $extensionId);
        }

        try {
            session()->forget(['pending_booking_group', 'pending_booking_ids']);
        } catch (\Throwable $e) {
            Log::warning('Failed to clear pending booking session: ' . $e->getMessage());
        }

        $roomBookings = collect();
        $boatBookings = collect();
        $depositPaid  = 0;

        if ($groupId) {
            $roomBookings = Booking::with('room')->where('group_id', $groupId)->get();
            $boatBookings = BoatBooking::with('boat')->where('group_id', $groupId)->get();
            $all          = $roomBookings->concat($boatBookings);
            $depositPaid  = $all->sum('paid_amount') ?: $all->sum('deposit_amount');
        } elseif ($extensionId) {
            $ext = \App\Models\BookingExtension::with('booking.room')->find($extensionId);
            if ($ext && $ext->booking) {
                $roomBookings = collect([$ext->booking]);
                $depositPaid = $ext->price;
            }
        }

        $bookings = $roomBookings->concat($boatBookings);

        return view('payments.booking-success', compact('bookings', 'groupId', 'depositPaid'));
    }

    public function success(Request $request)
    {
        // PayMongo may return guests here via the configured success URL.
        // Render the same success page directly so verification runs on this request.
        return $this->bookingSuccess($request);
    }

    public function cancel(Request $request)
    {
        $groupId = $request->query('group_id', session('pending_booking_group'));

        try {
            if ($groupId) {
                Booking::where('group_id', $groupId)
                    ->where('payment_status', 'pending')
                    ->whereIn('status', ['waiting', 'pending'])
                    ->delete();

                BoatBooking::where('group_id', $groupId)
                    ->where('payment_status', 'pending')
                    ->whereIn('status', ['waiting', 'pending'])
                    ->delete();
            }
            session()->forget(['pending_booking_group', 'pending_booking_ids']);
        } catch (\Throwable $e) {
            Log::warning('Failed to clean up cancelled bookings: ' . $e->getMessage());
        }

        return view('payments.cancel');
    }

    private function syncCheckoutSessionPayment(string $groupId): void
    {
        try {
            $sessionId = Booking::where('group_id', $groupId)->value('payment_id')
                ?: BoatBooking::where('group_id', $groupId)->value('payment_id');

            if (!$sessionId || !str_starts_with($sessionId, 'cs_')) {
                Log::info('Payment success sync skipped: no checkout session id found', [
                    'group_id' => $groupId,
                    'payment_id' => $sessionId,
                ]);
                return;
            }

            $response = $this->paymongo->getCheckoutSession($sessionId);
            if (empty($response['success'])) {
                Log::warning('Payment success sync could not retrieve checkout session', [
                    'group_id' => $groupId,
                    'session_id' => $sessionId,
                    'message' => $response['message'] ?? null,
                ]);
                return;
            }

            $paid = $this->paymongo->paidCheckoutSessionDetails($response['raw'] ?? []);
            if (!$paid) {
                Log::info('Payment success sync found checkout not paid yet', [
                    'group_id' => $groupId,
                    'session_id' => $sessionId,
                ]);
                return;
            }

            $result = $this->payments->markPaid(
                groupId: $groupId,
                sessionId: $sessionId,
                paymentId: $paid['payment_id'] ?? null,
                amountPaid: (float) ($paid['amount_php'] ?? 0)
            );

            if (($result['newly_paid_count'] ?? 0) > 0) {
                $this->payments->sendConfirmationMail($groupId);
            }
        } catch (\Throwable $e) {
            Log::error('Payment success sync failed', [
                'group_id' => $groupId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function returnUrl(?string $configuredUrl, string $fallbackUrl, array $query): string
    {
        // Favor dynamic fallbackUrl (generated via route()) over hardcoded .env config
        // to prevent session logout caused by host mismatches (e.g., localhost vs 127.0.0.1).
        $url = $configuredUrl ?: $fallbackUrl;
        $filteredQuery = array_filter($query, fn ($value) => $value !== null && $value !== '');

        if (empty($filteredQuery)) {
            return $url;
        }

        return $url . (str_contains($url, '?') ? '&' : '?') . http_build_query($filteredQuery);
    }
}
