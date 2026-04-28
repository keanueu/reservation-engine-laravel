<?php

namespace App\Http\Controllers;
use App\Models\Room;
use App\Models\Boat;
use App\Models\Booking;
use App\Models\BoatBooking;
use App\Models\Setting;
class CheckoutController extends Controller
{
    public function show()
    {
        // Support summary by group_id for unified bookings
        $groupId = request('group_id');
        $cartRooms = collect();
        $cartBoats = collect();
        $total = 0;
        if ($groupId) {
            $cartRooms = Booking::with('room')->where('group_id', $groupId)->get();
            $cartBoats = BoatBooking::with('boat')->where('group_id', $groupId)->get();
            $total = $cartRooms->sum('total_amount') + $cartBoats->sum('total_amount');
        } else {
            // fallback to session cart if no group_id
            $cart = session('cart', []); // array of cart items

            // Validate session cart rooms against existing bookings. If a room has been
            // booked by someone else for overlapping dates, remove it from the session
            // cart so the user cannot attempt to book an already-reserved room.
            $filtered = [];
            $removedRooms = [];
            foreach ($cart as $item) {
                if (isset($item['room_id']) && !empty($item['start_date']) && !empty($item['end_date'])) {
                    $roomId = $item['room_id'];
                    $start = $item['start_date'];
                    $end = $item['end_date'];

                    // Check for any overlapping bookings (exclude cancelled/rejected/waiting/pending)
                    // Only count confirmed/paid bookings as blocking availability
                    $overlap = Booking::where('room_id', $roomId)
                        ->whereNotIn('status', ['cancelled', 'rejected', 'waiting'])
                        ->where('payment_status', 'paid')
                        ->where(function ($q) use ($start, $end) {
                            $q->where('start_date', '<', $end)
                                ->where('end_date', '>', $start);
                        })->exists();

                    if ($overlap) {
                        // mark as removed and skip adding to filtered cart
                        try {
                            $r = Room::find($roomId);
                            $removedRooms[] = $r ? $r->room_name : ('Room #' . $roomId);
                        } catch (\Throwable $e) {
                            $removedRooms[] = 'Room #' . $roomId;
                        }
                        continue;
                    }
                }

                // keep items that are boats or non-overlapping rooms
                $filtered[] = $item;
            }

            // If any rooms were removed, persist cleaned cart and flash notice
            if (count($removedRooms) > 0) {
                session(['cart' => $filtered]);
                session()->flash('error', 'Some rooms were removed from your cart because they were already booked: ' . implode(', ', $removedRooms));
                $cart = $filtered;
            }
            $roomIds = collect($cart)->pluck('room_id')->all();
            $boatIds = collect($cart)->pluck('boat_id')->all();
            $cartRooms = Room::whereIn('id', $roomIds)->get();
            foreach ($cartRooms as $room) {
                $room->cart_data = collect($cart)->firstWhere('room_id', $room->id);
            }
            $cartBoats = Boat::whereIn('id', $boatIds)->get();
            foreach ($cartBoats as $boat) {
                $boat->cart_data = collect($cart)->firstWhere('boat_id', $boat->id);
            }
            // Use stored cart pricing (line_total / unit_price) when available so discounts apply correctly
            $total = 0;
            foreach ($cartRooms as $room) {
                $cart = $room->cart_data ?? [];
                $nights = $cart['nights'] ?? 1;
                // prefer line_total (already multiplied by nights), then unit_price * nights, then model price * nights
                $roomSubtotal = $cart['line_total'] ?? (($cart['unit_price'] ?? $room->price) * $nights);
                $total += $roomSubtotal;
            }
            foreach ($cartBoats as $boat) {
                $b = $boat->cart_data ?? [];
                $boatSubtotal = $b['price'] ?? $boat->price ?? 0;
                $total += $boatSubtotal;
            }
        }
        $depositPercent = (float) Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
        $deposit = $total * ($depositPercent / 100);

        return view('home.checkout', compact('cartRooms', 'cartBoats', 'total', 'deposit', 'groupId'));
    }

}
