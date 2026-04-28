<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BoatBooking;
use App\Models\Room;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Services\PaymongoService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BookingReceiptNotification;


class BookingController extends Controller
{
    public function store(Request $request)
    {
        $hasRooms = $request->filled('room_ids') && is_array($request->room_ids) && count($request->room_ids) > 0;
        $hasBoats = $request->filled('boat_ids') && is_array($request->boat_ids) && count($request->boat_ids) > 0;

        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
        ];

        // Validate room fields
        if ($hasRooms) {
            $rules = array_merge($rules, [
                'room_ids' => 'required|array',
                'start_dates' => 'required|array',
                'end_dates' => 'required|array',
                'adults' => 'required|array',
                'children' => 'required|array',
                'nights' => 'required|array',
            ]);
        }

        // Validate boat fields
        if ($hasBoats) {
            $rules = array_merge($rules, [
                'boat_ids' => 'required|array',
                'booking_dates' => 'required|array',
                'start_times' => 'required|array',
                'end_times' => 'required|array',
                'boat_guests' => 'required|array',
                'boat_prices' => 'required|array',
            ]);
        }

        if (!$hasRooms && !$hasBoats) {
            return back()->withErrors(['booking' => 'Please select at least one room or boat to book.']);
        }

        $request->validate($rules);

        // Shared group ID for this checkout session (rooms + boats)
        $groupId = Str::uuid();

        $total = 0;
        $bookings = [];

        // --- ROOM BOOKINGS ---
        if ($hasRooms) {
            $roomIds = array_slice($request->room_ids, 0, 3); // limit to 3 rooms max
            foreach ($roomIds as $i => $room_id) {
                $room = Room::find($room_id);
                if (!$room)
                    continue;

                $adults = (int) ($request->adults[$i] ?? 1);
                $children = (int) ($request->children[$i] ?? 0);
                $totalGuests = $adults + $children;
                $maxGuests = (int) $room->accommodates;

                if ($totalGuests > $maxGuests) {
                    return back()->with('error', 'Guest count for room "' . $room->room_name . '" exceeds accommodates (' . $maxGuests . '). Please adjust guest count.');
                }

                $nights = (int) ($request->nights[$i] ?? 1);
                // Prefer pricing from session cart (line_total / unit_price) so discounts/promos apply
                $sessionCart = session('cart', []);
                $cartItem = collect($sessionCart)->firstWhere('room_id', $room->id) ?: [];
                $subtotal = $cartItem['line_total'] ?? ((($cartItem['unit_price'] ?? $room->price) * $nights));
                $total += $subtotal;
                // capture applied discount (if any) for this room so we can show promo type later
                $appliedDiscount = $room->discounts->first() ?? null;
                $promoLabel = optional($appliedDiscount)->name ?? null;
                $appliedDiscountId = optional($appliedDiscount)->id ?? null;

                // Prefer scheduled checkin/checkout datetimes from the session cart (guest preferred times)
                // Fallback to request-provided times or business defaults when cart values are missing.
                $startDate = $request->start_dates[$i] ?? null;
                $endDate = $request->end_dates[$i] ?? null;
                $scheduledCheckin = null;
                $scheduledCheckout = null;

                // cartItem may contain pre-computed scheduled datetimes (from CartController)
                $cartScheduledIn = $cartItem['scheduled_checkin_at'] ?? null;
                $cartScheduledOut = $cartItem['scheduled_checkout_at'] ?? null;

                try {
                    if ($cartScheduledIn) {
                        $scheduledCheckin = \Illuminate\Support\Carbon::parse($cartScheduledIn);
                    } else {
                        $checkinTime = $request->input('checkin_time') ?? config('booking.checkin_time', '13:00');
                        if ($startDate) {
                            $scheduledCheckin = \Illuminate\Support\Carbon::parse($startDate . ' ' . $checkinTime);
                        }
                    }

                    if ($cartScheduledOut) {
                        $scheduledCheckout = \Illuminate\Support\Carbon::parse($cartScheduledOut);
                    } else {
                        $checkoutTime = $request->input('checkout_time') ?? config('booking.checkout_time', '11:00');
                        if ($endDate) {
                            $scheduledCheckout = \Illuminate\Support\Carbon::parse($endDate . ' ' . $checkoutTime);
                        }
                    }
                } catch (\Exception $e) {
                    // ignore parse errors and leave datetime null
                }

                $booking = Booking::create([
                    'group_id' => $groupId,
                    'room_id' => $room->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'adults' => $adults,
                    'children' => $children,
                    'start_date' => $startDate ?? null,
                    'end_date' => $endDate ?? null,
                    'scheduled_checkin_at' => $scheduledCheckin,
                    'scheduled_checkout_at' => $scheduledCheckout,
                    'nights' => $nights,
                    'status' => 'waiting',
                    'payment_status' => 'pending',
                    'total_amount' => $subtotal,
                    'promo_label' => $promoLabel,
                    'discount_id' => $appliedDiscountId,
                ]);

                $bookings[] = $booking;
            }
        }

        // --- BOAT BOOKINGS ---
        if ($hasBoats) {
            $boatIds = $request->boat_ids ?? [];
            $bookingDates = $request->booking_dates ?? [];
            $startTimes = $request->start_times ?? [];
            $endTimes = $request->end_times ?? [];
            $boatGuests = $request->boat_guests ?? [];
            $boatPrices = $request->boat_prices ?? [];

            foreach ($boatIds as $i => $boat_id) {
                $boatPrice = (float) ($boatPrices[$i] ?? 0);
                $total += $boatPrice;

                $boatBooking = BoatBooking::create([
                    'group_id' => $groupId,
                    'boat_id' => $boat_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'booking_date' => $bookingDates[$i] ?? null,
                    'start_time' => $startTimes[$i] ?? null,
                    'end_time' => $endTimes[$i] ?? null,
                    'guests' => $boatGuests[$i] ?? 1,
                    'status' => 'waiting',
                    'payment_status' => 'pending',
                    'total_amount' => $boatPrice,
                ]);
                // ensure we have a reference booking for redirect (supports boats-only checkout)
                $bookings[] = $boatBooking;
            }
        }

        // --- Minimum amount check ---
        if ($total < 100) {
            return back()->with('error', 'Minimum total amount is ₱100. Please add more rooms or boats.');
        }

        // --- Redirect to unified payment link ---
        if (count($bookings) > 0) {
            // Charge only the configured deposit percentage at checkout
            // Charge deposit + deposit fee at checkout
            // Use dynamic settings (frontdesk editable) when available
            $depositPercent = (float) \App\Models\Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
            // Booking-time deposit fee disabled by default; refund fee is applied on refund flows
            $depositFeePercent = (float) \App\Models\Setting::get('deposit_fee_percentage', config('booking.deposit_fee_percentage', 0));

            // Calculate deposit
            $depositAmount = $total * ($depositPercent / 100);

            // Calculate deposit fee
            $depositFee = $depositAmount * ($depositFeePercent / 100);

            // Total amount to charge now (deposit + fee)
            $totalToCharge = round($depositAmount + $depositFee, 2);

            // Optional: store deposit/fee per booking for reference
            // Calculate deposit per booking based on that booking's total_amount
            foreach ($bookings as $booking) {
                $ba = (float) ($booking->total_amount ?? 0);
                $depositForBooking = round($ba * ($depositPercent / 100), 2);
                $depositFeeForBooking = round($depositForBooking * ($depositFeePercent / 100), 2);
                $totalToChargeForBooking = round($depositForBooking + $depositFeeForBooking, 2);

                $booking->deposit_amount = $depositForBooking;
                $booking->deposit_fee = $depositFeeForBooking;
                $booking->total_to_charge = $totalToChargeForBooking;
                $booking->save();
            }

            // Persist pending booking group and booking IDs in session so they can be
            // cleaned up if the user cancels/returns without completing payment.
            try {
                $bookingIds = collect($bookings)->pluck('id')->all();
                session([
                    'pending_booking_group' => $groupId,
                    'pending_booking_ids' => $bookingIds,
                ]);
                
                // Clear the cart since bookings have been created
                session()->forget('cart');
            } catch (\Throwable $e) {
                // non-fatal: session store failed — continue to redirect to payment
            }


            // --- SEND NOTIF HERE ---
            try {
                Notification::route('mail', $request->email)
                    ->notify(new BookingReceiptNotification($bookings, 'booking'));
            } catch (\Throwable $e) {
                Log::error('Booking receipt email failed', [
                    'error' => $e->getMessage(),
                    'email' => $request->email
                ]);
            }

            // Redirect to payment link
            return redirect()->route('bookings.pay', [
                'booking' => $bookings[0]->id,
                'group_id' => $groupId,
                'amount' => $totalToCharge
            ]);


        } else {
            return back()->with('error', 'No room bookings were created.');
        }

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
