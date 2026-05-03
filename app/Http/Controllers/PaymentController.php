<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BoatBooking;
use App\Services\PaymongoService;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log; 
class PaymentController extends Controller
{
    protected $paymongo;

    public function __construct(PaymongoService $paymongo)
    {
        $this->paymongo = $paymongo;
    }

    public function payForBooking(Request $request)
    {
        $groupId = $request->query('group_id');
        $amountPhp = (float) $request->query('amount', 0);

        $roomBookings = collect();
        $boatBookings = collect();

        if ($groupId) {
            $roomBookings = Booking::with('room')->where('group_id', $groupId)->get();
            $boatBookings = BoatBooking::with('boat')->where('group_id', $groupId)->get();
        }

        // Use either a room booking or a boat booking as the main reference
        $booking = $roomBookings->first() ?: $boatBookings->first();

        if (!$booking) {
            return redirect()->back()->with('error', 'No booking data found for payment.');
        }

        // --- Compute total ---
        if (!$amountPhp) {
            $fullTotal = $roomBookings->sum('total_amount') + $boatBookings->sum('total_amount');
            $depositPercent = (float) Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
            $amountPhp = $fullTotal * ($depositPercent / 100);
            // Attach deposit info to logs/metadata


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
        $successUrl = route('booking.success', ['group_id' => $groupId]);
        $cancelUrl  = route('payment.cancel',  ['group_id' => $groupId]);

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
        }

        $bookings = $roomBookings->concat($boatBookings);

        return view('payments.booking-success', compact('bookings', 'groupId', 'depositPaid'));
    }

    public function success(Request $request)
    {
        // Legacy redirect — forward to the new booking success page
        return redirect()->route('booking.success', $request->query());
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
}
