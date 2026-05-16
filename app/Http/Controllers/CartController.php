<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Boat;
use App\Models\Booking;
use App\Models\BoatBooking;
use App\Support\CartHelper;
use Carbon\Carbon;

class CartController extends Controller
{
    // -------------------------------------------------------------------------
    // ROOM CART
    // -------------------------------------------------------------------------

    public function add(Request $request, $room_id)
    {
        $room = Room::with('discounts')->findOrFail($room_id);
        $cart = session('cart', []);

        $startDate  = $request->input('startDate');
        $endDate    = $request->input('endDate');
        $adults     = (int) $request->input('adults');
        $children   = (int) $request->input('children');
        $startTime  = $request->input('start_time');
        $endTime    = $request->input('end_time');

        // Guest capacity check
        if (($adults + $children) > (int) $room->accommodates) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Guest count exceeds room capacity. Please adjust.',
            ], 422);
        }

        // Times required
        if (empty($startTime) || empty($endTime)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Please provide both check-in and check-out times.',
            ], 422);
        }

        // Real-time availability check (soft — final atomic check is in BookingController@store)
        if ($startDate && $endDate) {
            $conflict = Booking::availableBetween($startDate, $endDate)
                ->where('room_id', $room->id)
                ->exists();

            if ($conflict) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'This room is already booked for the selected dates.',
                ], 409);
            }
        }

        // Night calculation
        $nights = 1;
        if ($startDate && $endDate) {
            $diff = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
            $nights = max(1, $diff);
        }

        // Pricing with discount
        $discount      = $room->discounts->first();
        $discountValue = optional($discount)->amount ?? 0;
        $discountType  = optional($discount)->amount_type ?? null;
        $isActive      = optional($discount)->active ?? false;
        $unitPrice     = (float) $room->price;

        if ($isActive && $discountValue > 0) {
            if (in_array($discountType, ['percent', 'percentage'])) {
                $unitPrice = $unitPrice * (1 - $discountValue / 100);
            } elseif ($discountType === 'fixed') {
                $unitPrice = max(0, $unitPrice - $discountValue);
            }
        }

        $newItem = [
            'type'                  => 'room',
            'room_id'               => $room->id,
            'start_date'            => $startDate,
            'end_date'              => $endDate,
            'start_time'            => $startTime,
            'end_time'              => $endTime,
            'scheduled_checkin_at'  => $startDate ? Carbon::parse($startDate . ' ' . ($startTime ?: '13:00'))->toDateTimeString() : null,
            'scheduled_checkout_at' => $endDate   ? Carbon::parse($endDate   . ' ' . ($endTime   ?: '11:00'))->toDateTimeString() : null,
            'adults'                => $adults,
            'children'              => $children,
            'nights'                => $nights,
            'original_unit_price'   => (float) $room->price,
            'unit_price'            => $unitPrice,
            'discount'              => $discountValue,
            'discount_type'         => $discountType,
            'line_total'            => $unitPrice * $nights,
        ];

        // Check for overlapping dates in the user's cart
        foreach ($cart as $item) {
            if (isset($item['room_id'], $item['start_date'], $item['end_date']) && $item['room_id'] == $room->id) {
                $existingStart = Carbon::parse($item['start_date']);
                $existingEnd   = Carbon::parse($item['end_date']);
                $newStart      = Carbon::parse($startDate);
                $newEnd        = Carbon::parse($endDate);

                // Check overlap: Start A < End B AND End A > Start B
                if ($newStart < $existingEnd && $newEnd > $existingStart) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'This room is already in your cart for these dates. Please remove the existing item from your cart if you wish to modify the booking.',
                    ], 409);
                }
            }
        }

        // If no overlap, append to cart
        $cart[] = $newItem;

        session(['cart' => $cart]);

        return response()->json([
            'status'    => 'success',
            'message'   => 'Room added to cart.',
            'cart_html' => CartHelper::renderPartial('home.partials.cart-summary', $cart),
            'cart_count' => count($cart),
        ]);
    }

    public function getItems()
    {
        return response()->json(['items' => session('cart', [])]);
    }

    public function remove($room_id)
    {
        $cart = array_values(array_filter(
            session('cart', []),
            fn($item) => !(isset($item['room_id']) && $item['room_id'] == $room_id)
        ));

        session(['cart' => $cart]);

        // Clean up any pending DB bookings for this room in the current session group
        $this->cleanupPendingRoomBookings($room_id);

        $partial = CartHelper::detectPartial();

        return response()->json([
            'status'    => 'success',
            'cart_html' => CartHelper::renderPartial($partial, $cart),
            'cart_count' => count($cart),
        ]);
    }

    // -------------------------------------------------------------------------
    // BOAT CART
    // -------------------------------------------------------------------------

    public function checkBoatAvailability(Request $request)
    {
        $boat = Boat::find($request->input('boat_id'));
        if (!$boat) {
            return response()->json(['available' => false, 'message' => 'Boat not found.'], 404);
        }

        $guests = (int) $request->input('guests');
        if ($guests > $boat->capacity) {
            return response()->json(['available' => false, 'message' => 'Guest count exceeds boat capacity.'], 422);
        }

        $bookingDate = $request->input('booking_date');
        $startTime   = $request->input('start_time');
        $endTime     = $request->input('end_time');

        $overlap = BoatBooking::where('boat_id', $boat->id)
            ->where('booking_date', $bookingDate)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->where(fn($q) => $q->where('start_time', '<', $endTime)->where('end_time', '>', $startTime))
            ->exists();

        return response()->json($overlap
            ? ['available' => false, 'message' => 'Boat is not available for the selected time.']
            : ['available' => true]
        );
    }

    public function addBoatToCart(Request $request, $boat_id)
    {
        $boat = Boat::find($boat_id);
        if (!$boat) {
            return response()->json(['status' => 'error', 'message' => 'Boat not found.'], 404);
        }

        $guests = (int) $request->input('guests');
        if ($guests > $boat->capacity) {
            return response()->json(['status' => 'error', 'message' => 'Guest count exceeds boat capacity.'], 422);
        }

        $cart   = session('cart', []);
        $cart[] = [
            'type'         => 'boat',
            'boat_id'      => $boat->id,
            'booking_date' => $request->input('booking_date'),
            'start_time'   => $request->input('start_time'),
            'end_time'     => $request->input('end_time'),
            'guests'       => $guests,
            'price'        => (float) $boat->price,
        ];

        session(['cart' => $cart]);

        return response()->json([
            'status'    => 'success',
            'cart_html' => CartHelper::renderPartial('home.partials.cart-summary', $cart),
            'cart_count' => count($cart),
        ]);
    }

    public function removeBoatFromCart($boat_id)
    {
        $cart = array_values(array_filter(
            session('cart', []),
            fn($item) => !(isset($item['boat_id']) && $item['boat_id'] == $boat_id)
        ));

        session(['cart' => $cart]);

        $this->cleanupPendingBoatBookings($boat_id);

        $partial = CartHelper::detectPartial();

        return response()->json([
            'status'    => 'success',
            'cart_html' => CartHelper::renderPartial($partial, $cart),
            'cart_count' => count($cart),
        ]);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function cleanupPendingRoomBookings(int|string $roomId): void
    {
        try {
            $group = session('pending_booking_group');
            if (!$group) return;

            $exists = Booking::where('group_id', $group)->where('room_id', $roomId)->exists();
            if (!$exists) return;

            Booking::where('group_id', $group)
                ->where(fn($q) => $q->where('payment_status', 'pending')
                    ->orWhereNull('payment_status')
                    ->orWhere('status', 'waiting'))
                ->delete();

            session()->forget(['pending_booking_group', 'pending_booking_ids']);
        } catch (\Throwable $e) {
            \Log::warning('CartController: pending room booking cleanup failed — ' . $e->getMessage());
        }
    }

    private function cleanupPendingBoatBookings(int|string $boatId): void
    {
        try {
            $group = session('pending_booking_group');
            if (!$group) return;

            $exists = BoatBooking::where('group_id', $group)->where('boat_id', $boatId)->exists();
            if (!$exists) return;

            BoatBooking::where('group_id', $group)
                ->where(fn($q) => $q->where('payment_status', 'pending')
                    ->orWhereNull('payment_status')
                    ->orWhere('status', 'waiting'))
                ->delete();

            session()->forget(['pending_booking_group', 'pending_booking_ids']);
        } catch (\Throwable $e) {
            \Log::warning('CartController: pending boat booking cleanup failed — ' . $e->getMessage());
        }
    }
}
