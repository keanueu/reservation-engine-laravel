<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BoatBooking;
use App\Models\Room;
use App\Models\Boat;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Services\CheckoutService;
use App\Services\RefundService;
use App\DTOs\CheckoutPayloadDTO;
use App\DTOs\RefundRequestDTO;

class BookingController extends Controller
{
    public function store(Request $request, CheckoutService $checkoutService)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
        ]);

        $cart = session('cart', []);
        $payload = CheckoutPayloadDTO::fromRequestAndCart($validated, $cart);

        if ($payload->isEmpty()) {
            return back()->withErrors(['booking' => 'Your cart is empty. Please add a room or boat first.']);
        }

        if (count($payload->roomItems) > 5) {
            return back()->with('error', 'You can book a maximum of 5 rooms per checkout.');
        }

        try {
            $result = $checkoutService->processCheckout($payload);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('BookingController@store failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'An unexpected error occurred. Please try again.');
        }

        try {
            session([
                'pending_booking_group' => $result['groupId'],
                'pending_booking_ids'   => collect($result['bookings'])->pluck('id')->all(),
            ]);
            session()->forget('cart');
        } catch (\Throwable $e) { /* non-fatal */ }

        return redirect()->route('bookings.pay', [
            'booking'  => $result['firstBookingId'],
            'group_id' => $result['groupId'],
            'amount'   => $result['totalToCharge'],
        ]);
    }

    /**
     * User: request a refund for a booking (sets refund_status = 'requested')
     */
    public function requestRefund(Request $request, $id, RefundService $refundService)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $booking = Booking::find($id);
        if (!$booking) {
            $msg = 'Booking not found.';
            return ($request->ajax() || $request->wantsJson()) ? response()->json(['message' => $msg], 404) : back()->with('error', $msg);
        }

        $user = auth()->user();
        $isStaff = $user && isset($user->usertype) && in_array($user->usertype, ['frontdesk', 'admin']);
        $payload = RefundRequestDTO::fromRequest($validated);

        try {
            $refundService->requestRefund($booking, $payload, $user->email ?? null, $isStaff);
        } catch (\RuntimeException $e) {
            return ($request->ajax() || $request->wantsJson()) ? response()->json(['message' => $e->getMessage()], 400) : back()->with('error', $e->getMessage());
        }

        $success = 'Refund request submitted. Frontdesk will review it shortly.';
        return ($request->ajax() || $request->wantsJson()) ? response()->json(['message' => $success]) : back()->with('success', $success);
    }

    public function adminApproveRefund(Request $request, $id, RefundService $refundService)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return back()->with('error', 'Booking not found.');
        }

        try {
            $refundService->approveRefund($booking);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Booking marked as refunded.');
    }

    public function adminRejectRefund(Request $request, $id, RefundService $refundService)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return back()->with('error', 'Booking not found.');
        }

        $refundService->rejectRefund($booking);
        return back()->with('success', 'Refund request rejected.');
    }




}
