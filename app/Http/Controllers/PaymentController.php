<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BoatBooking;
use App\Services\PaymongoService;
use Illuminate\Http\Request;
use App\Models\Setting; 
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
            'description' => "Booking Group {$groupId} - {$fullDescription}",
            'customer_name' => $booking->name ?? 'Unknown',
            'customer_email' => $booking->email ?? 'Unknown',
            'group_id' => $groupId,
        ];

        // --- Create PayMongo link ---
        $response = $this->paymongo->createLink($amount, 'PHP', $metadata);

        // `createLink` returns a normalized wrapper: ['success'=>bool,'link_id'=>..., 'checkout_url'=>..., 'raw'=>...]
        $success = $response['success'] ?? false;
        $id = $response['link_id'] ?? null;
        $checkoutUrl = $response['checkout_url'] ?? null;

        if (!$success) {
            \Log::error('PayMongo createLink failed', ['group_id' => $groupId, 'resp' => $response]);
        }

        // --- Save payment_id ---
        if ($groupId && $id) {
            Booking::where('group_id', $groupId)->update(['payment_id' => $id]);
            BoatBooking::where('group_id', $groupId)->update(['payment_id' => $id]);
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

    public function success()
    {
        // Clear any pending booking markers from session after successful payment
        try {
            session()->forget(['pending_booking_group', 'pending_booking_ids']);
        } catch (\Throwable $e) {
            \Log::warning('Failed to clear pending booking session after success: ' . $e->getMessage());
        }
        return view('payments.success');
    }

    public function cancel()
    {
        // Clean up abandoned bookings when user cancels payment
        try {
            $pendingGroup = session('pending_booking_group');
            if ($pendingGroup) {
                // Delete all unpaid bookings in this group
                Booking::where('group_id', $pendingGroup)
                    ->where('payment_status', 'pending')
                    ->whereIn('status', ['waiting', 'pending'])
                    ->delete();
                
                BoatBooking::where('group_id', $pendingGroup)
                    ->where('payment_status', 'pending')
                    ->whereIn('status', ['waiting', 'pending'])
                    ->delete();
            }
            
            session()->forget(['pending_booking_group', 'pending_booking_ids']);
        } catch (\Throwable $e) {
            \Log::warning('Failed to clear pending booking session after cancel: ' . $e->getMessage());
        }
        return view('payments.cancel');
    }
}
