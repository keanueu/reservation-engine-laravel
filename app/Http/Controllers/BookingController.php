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
use App\Services\PaymongoService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        // -------------------------------------------------------
        // 1. Validate guest info fields
        // -------------------------------------------------------
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
        ]);

        // -------------------------------------------------------
        // 2. Read the cart — this is the single source of truth
        // -------------------------------------------------------
        $cart = session('cart', []);

        $roomItems = collect($cart)->filter(fn($i) => isset($i['room_id']))->values();
        $boatItems = collect($cart)->filter(fn($i) => isset($i['boat_id']))->values();

        if ($roomItems->isEmpty() && $boatItems->isEmpty()) {
            return back()->withErrors(['booking' => 'Your cart is empty. Please add a room or boat first.']);
        }

        // Hard cap: max 5 rooms + 3 boats per checkout
        if ($roomItems->count() > 5) {
            return back()->with('error', 'You can book a maximum of 5 rooms per checkout.');
        }

        $groupId  = \Illuminate\Support\Str::uuid()->toString();
        $total    = 0.0;
        $bookings = [];

        // -------------------------------------------------------
        // 3. Single atomic transaction — ALL rooms + boats or NONE
        // -------------------------------------------------------
        try {
            DB::transaction(function () use (
                $request, $roomItems, $boatItems,
                $groupId, &$total, &$bookings
            ) {
                // ---------------------------------------------------
                // PHASE A: Lock ALL room rows first (sorted by ID to
                // prevent deadlocks between concurrent transactions)
                // ---------------------------------------------------
                $sortedRoomItems = $roomItems->sortBy('room_id');

                foreach ($sortedRoomItems as $cartItem) {
                    $room_id   = $cartItem['room_id'];
                    $startDate = $cartItem['start_date'] ?? null;
                    $endDate   = $cartItem['end_date']   ?? null;

                    if (!$startDate || !$endDate) {
                        throw new \RuntimeException('Missing dates for one of the rooms in your cart.');
                    }

                    // Lock the room row — blocks concurrent transactions on same room
                    $room = Room::lockForUpdate()->find($room_id);
                    if (!$room) {
                        throw new \RuntimeException('A room in your cart no longer exists.');
                    }

                    // Guest capacity check
                    $adults      = (int) ($cartItem['adults']   ?? 1);
                    $children    = (int) ($cartItem['children'] ?? 0);
                    $maxGuests   = (int) $room->accommodates;

                    if (($adults + $children) > $maxGuests) {
                        throw new \RuntimeException(
                            'Guest count for "' . $room->room_name . '" exceeds capacity (' . $maxGuests . ').'
                        );
                    }

                    // Atomic availability check — lock conflicting booking rows too
                    $conflict = Booking::availableBetween($startDate, $endDate)
                        ->where('room_id', $room->id)
                        ->lockForUpdate()
                        ->exists();

                    if ($conflict) {
                        throw new \RuntimeException(
                            'Room "' . $room->room_name . '" is no longer available for ' .
                            $startDate . ' – ' . $endDate . '. Please choose different dates.'
                        );
                    }

                    // Pricing — trust the cart (already has discount applied)
                    $nights   = (int) ($cartItem['nights'] ?? 1);
                    $subtotal = (float) ($cartItem['line_total'] ?? (($cartItem['unit_price'] ?? $room->price) * $nights));
                    $total   += $subtotal;

                    // Scheduled datetimes
                    $scheduledCheckin  = null;
                    $scheduledCheckout = null;
                    try {
                        $scheduledCheckin  = $cartItem['scheduled_checkin_at']  ? Carbon::parse($cartItem['scheduled_checkin_at'])  : Carbon::parse($startDate . ' ' . ($cartItem['start_time'] ?? '13:00'));
                        $scheduledCheckout = $cartItem['scheduled_checkout_at'] ? Carbon::parse($cartItem['scheduled_checkout_at']) : Carbon::parse($endDate   . ' ' . ($cartItem['end_time']   ?? '11:00'));
                    } catch (\Exception $e) { /* leave null */ }

                    $appliedDiscount   = $room->discounts->first() ?? null;

                    $booking = Booking::create([
                        'group_id'              => $groupId,
                        'room_id'               => $room->id,
                        'name'                  => $request->name,
                        'email'                 => $request->email,
                        'phone'                 => $request->phone,
                        'adults'                => $adults,
                        'children'              => $children,
                        'start_date'            => $startDate,
                        'end_date'              => $endDate,
                        'scheduled_checkin_at'  => $scheduledCheckin,
                        'scheduled_checkout_at' => $scheduledCheckout,
                        'nights'                => $nights,
                        'status'                => 'waiting',
                        'payment_status'        => 'pending',
                        'total_amount'          => $subtotal,
                        'promo_label'           => optional($appliedDiscount)->name,
                        'discount_id'           => optional($appliedDiscount)->id,
                        'expires_at'            => now()->addMinutes(Booking::PENDING_TTL_MINUTES),
                    ]);

                    $bookings[] = $booking;
                }

                // ---------------------------------------------------
                // PHASE B: Lock ALL boat rows (sorted by ID)
                // ---------------------------------------------------
                $sortedBoatItems = $boatItems->sortBy('boat_id');

                foreach ($sortedBoatItems as $cartItem) {
                    $boat_id     = $cartItem['boat_id'];
                    $bookingDate = $cartItem['booking_date'] ?? null;
                    $startTime   = $cartItem['start_time']   ?? null;
                    $endTime     = $cartItem['end_time']     ?? null;

                    $boat = Boat::lockForUpdate()->find($boat_id);
                    if (!$boat) {
                        throw new \RuntimeException('A boat in your cart no longer exists.');
                    }

                    $boatConflict = BoatBooking::where('boat_id', $boat_id)
                        ->where('booking_date', $bookingDate)
                        ->whereNotIn('status', ['cancelled', 'rejected'])
                        ->where(fn($q) => $q->where('start_time', '<', $endTime)->where('end_time', '>', $startTime))
                        ->lockForUpdate()
                        ->exists();

                    if ($boatConflict) {
                        throw new \RuntimeException(
                            'Boat "' . $boat->name . '" is not available for the selected time slot.'
                        );
                    }

                    $boatPrice = (float) ($cartItem['price'] ?? $boat->price);
                    $total    += $boatPrice;

                    $boatBooking = BoatBooking::create([
                        'group_id'       => $groupId,
                        'boat_id'        => $boat_id,
                        'name'           => $request->name,
                        'email'          => $request->email,
                        'phone'          => $request->phone,
                        'booking_date'   => $bookingDate,
                        'start_time'     => $startTime,
                        'end_time'       => $endTime,
                        'guests'         => $cartItem['guests'] ?? 1,
                        'status'         => 'waiting',
                        'payment_status' => 'pending',
                        'total_amount'   => $boatPrice,
                    ]);

                    $bookings[] = $boatBooking;
                }

                // ---------------------------------------------------
                // PHASE C: Minimum amount guard
                // ---------------------------------------------------
                if ($total < 100) {
                    throw new \RuntimeException('Minimum total is ₱100. Please add more items.');
                }

                // ---------------------------------------------------
                // PHASE D: Persist deposit fields on every booking
                // ---------------------------------------------------
                $depositPercent    = (float) \App\Models\Setting::get('deposit_percentage',     config('booking.deposit_percentage',     50));
                $depositFeePercent = (float) \App\Models\Setting::get('deposit_fee_percentage', config('booking.deposit_fee_percentage', 0));

                foreach ($bookings as $b) {
                    $ba  = (float) ($b->total_amount ?? 0);
                    $dep = round($ba * ($depositPercent    / 100), 2);
                    $fee = round($dep * ($depositFeePercent / 100), 2);

                    $b->deposit_amount  = $dep;
                    $b->deposit_fee     = $fee;
                    $b->total_to_charge = round($dep + $fee, 2);
                    $b->save();
                }

            }); // end DB::transaction — ALL committed or ALL rolled back

        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('BookingController@store failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'An unexpected error occurred. Please try again.');
        }

        // -------------------------------------------------------
        // 4. Post-transaction: session cleanup then redirect to payment
        // NOTE: Receipt email is sent by the webhook ONLY after payment_status = 'paid'
        // -------------------------------------------------------
        try {
            session([
                'pending_booking_group' => $groupId,
                'pending_booking_ids'   => collect($bookings)->pluck('id')->all(),
            ]);
            session()->forget('cart');
        } catch (\Throwable $e) { /* non-fatal */ }

        $depositPercent    = (float) \App\Models\Setting::get('deposit_percentage',     config('booking.deposit_percentage',     50));
        $depositFeePercent = (float) \App\Models\Setting::get('deposit_fee_percentage', config('booking.deposit_fee_percentage', 0));
        $depositAmount     = $total * ($depositPercent    / 100);
        $depositFee        = $depositAmount * ($depositFeePercent / 100);
        $totalToCharge     = round($depositAmount + $depositFee, 2);

        return redirect()->route('bookings.pay', [
            'booking'  => $bookings[0]->id,
            'group_id' => $groupId,
            'amount'   => $totalToCharge,
        ]);
    }

    /**
     * User: request a refund for a booking (sets refund_status = 'requested')
     */
    public function requestRefund(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $booking = Booking::find($id);
        if (!$booking) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Booking not found.'], 404);
            }
            return back()->with('error', 'Booking not found.');
        }

        // Optional: ensure authenticated user's email matches booking email OR the user is admin/frontdesk
        if (auth()->check()) {
            $user = auth()->user();
            $isStaff = isset($user->usertype) && in_array($user->usertype, ['frontdesk', 'admin']);
            if (!$isStaff && isset($user->email) && $booking->email !== $user->email) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['message' => 'You are not allowed to request a refund for this booking.'], 403);
                }
                return back()->with('error', 'You are not allowed to request a refund for this booking.');
            }
        }

        // Enforce one refund per booking group: if any booking in the same group already has a refund requested/processing/refunded, disallow another
        if (!empty($booking->group_id)) {
            $groupBookings = Booking::where('group_id', $booking->group_id)->get();
            $groupHasRefund = $groupBookings->contains(function ($item) {
                return in_array(strtolower($item->refund_status ?? ''), ['requested', 'processing', 'refunded']);
            });
            if ($groupHasRefund) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['message' => 'A refund has already been requested for this booking group. Only one refund request is allowed.'], 400);
                }
                return back()->with('error', 'A refund has already been requested for this booking group. Only one refund request is allowed.');
            }
        }

        // Only allow refund request if booking was paid
        if (($booking->payment_status ?? '') !== 'paid') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Refund can only be requested for paid bookings.'], 400);
            }
            return back()->with('error', 'Refund can only be requested for paid bookings.');
        }

        // Apply business rule: collect 5% refund fee. Store requested amount, fee, and net refund amount.
        $requested = null;
        if ($request->filled('amount')) {
            $requested = (float) $request->input('amount');
        } else {
            // fallback to paid amount when user didn't specify
            $requested = (float) ($booking->paid_amount ?? $booking->total_amount ?? 0);
        }

        // Determine maximum refundable amount (cap to total deposited amount across the booking group)
        $depositPercent = (float) config('booking.deposit_percentage', 50) / 100;
        $groupDepositTotal = 0;
        if (!empty($booking->group_id)) {
            $groupBookings = Booking::where('group_id', $booking->group_id)->get();
            foreach ($groupBookings as $gb) {
                $bDeposit = $gb->deposit_amount ?? round(($gb->total_amount ?? 0) * $depositPercent, 2);
                $groupDepositTotal += (float) $bDeposit;
            }
        } else {
            $groupDepositTotal = (float) ($booking->deposit_amount ?? ($booking->total_amount * $depositPercent));
        }

        if ($requested > $groupDepositTotal) {
            // For AJAX, return JSON; for normal requests, redirect back with error
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Requested refund cannot exceed total deposited amount for your rooms (' . number_format($groupDepositTotal, 2) . ').'], 400);
            }
            return back()->with('error', 'Requested refund cannot exceed total deposited amount for your rooms (₱' . number_format($groupDepositTotal, 2) . ').');
        }

        // Refund fee percentage is configurable via frontdesk settings (default 5%)
        $feePercent = (float) \App\Models\Setting::get('refund_fee_percentage', 5) / 100;
        $fee = round($requested * $feePercent, 2);
        $net = round(max(0, $requested - $fee), 2);

        $booking->refund_status = 'requested';
        $booking->refund_requested_amount = $requested;
        $booking->refund_fee = $fee;
        $booking->refund_amount = $net; // net amount to be refunded if approved
        $booking->refund_reason = $request->input('reason');
        $booking->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Refund request submitted. Frontdesk will review it shortly.']);
        }

        return back()->with('success', 'Refund request submitted. Frontdesk will review it shortly.');
    }

    /**
     * Frontdesk: approve and mark booking as refunded (does not call payment gateway here).
     */
    public function adminApproveRefund(Request $request, $id)
    {
        // require auth (routes grouped with auth)
        $booking = Booking::find($id);
        if (!$booking) {
            return back()->with('error', 'Booking not found.');
        }

        // Determine refund amount: prefer explicit refund_amount, fallback to paid_amount or total_amount
        $amount = $booking->refund_amount ?? $booking->paid_amount ?? $booking->total_amount ?? 0;

        // Set the refund amount on the model
        $booking->refund_amount = $amount;

        // Use config toggle to decide whether to call the payment gateway for refunds.
        $useGateway = config('booking.refund_via_gateway', false);

        // If gateway refund is enabled and we have a payment id, attempt gateway refund.
        if ($useGateway && !empty($booking->payment_id)) {
            // mark as processing when we will attempt gateway refund
            $booking->refund_status = 'processing';
            $booking->save();

            $service = new PaymongoService();
            // PayMongo expects amount in cents
            $amountCents = (int) round($amount * 100);
            $result = $service->refundPayment($booking->payment_id, $amountCents, $booking->refund_reason ?? null);

            if (!empty($result['success'])) {
                // update booking with refund id if column exists
                if (Schema::hasColumn('bookings', 'paymongo_refund_id')) {
                    $booking->paymongo_refund_id = $result['refund_id'] ?? null;
                }
                $booking->refund_status = 'refunded';
                $booking->refunded_at = now();
                $booking->save();

                return back()->with('success', 'Booking refunded successfully via PayMongo.');
            }

            // Log failure and mark accordingly
            Log::error('PayMongo refund failed', ['booking_id' => $booking->id, 'response' => $result]);
            $booking->refund_status = 'refund_failed';
            $booking->save();

            return back()->with('error', 'PayMongo refund failed: ' . (is_array($result['message'] ?? null) ? json_encode($result['message']) : ($result['message'] ?? 'Unknown error')));
        }

        // No gateway refund attempted (either disabled or no payment id): mark as refunded locally only
        $booking->refund_status = 'refunded';
        $booking->refunded_at = now();
        $booking->save();

        return back()->with('success', 'Booking marked as refunded (no gateway refund performed).');
    }

    /**
     * Frontdesk: reject a refund request
     */
    public function adminRejectRefund(Request $request, $id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return back()->with('error', 'Booking not found.');
        }

        $booking->refund_status = 'rejected';
        $booking->save();

        return back()->with('success', 'Refund request rejected.');
    }




}
