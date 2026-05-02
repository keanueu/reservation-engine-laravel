<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Boat;
use App\Models\Booking;
use App\Models\Setting;

class CartPageController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);

        // Validate cart items against existing bookings and remove overlaps
        $filtered = [];
        $removedRooms = [];

        foreach ($cart as $item) {
            if (isset($item['room_id']) && !empty($item['start_date']) && !empty($item['end_date'])) {
                $roomId = $item['room_id'];
                $start = $item['start_date'];
                $end = $item['end_date'];

                $overlap = Booking::where('room_id', $roomId)
                    ->whereNotIn('status', ['cancelled', 'rejected'])
                    ->where(function ($q) use ($start, $end) {
                        $q->where('start_date', '<', $end)
                          ->where('end_date', '>', $start);
                    })->exists();

                if ($overlap) {
                    try {
                        $r = Room::find($roomId);
                        $removedRooms[] = $r ? $r->room_name : ('Room #' . $roomId);
                    } catch (\Throwable $e) {
                        $removedRooms[] = 'Room #' . $roomId;
                    }
                    continue;
                }
            }

            $filtered[] = $item;
        }

        if (count($removedRooms) > 0) {
            session(['cart' => $filtered]);
            session()->flash('error', 'Some rooms were removed from your cart because they were already booked: ' . implode(', ', $removedRooms));
            $cart = $filtered;
        }

        // Build cart collections
        $cartRooms = collect();
        $cartBoats = collect();

        foreach ($cart as $item) {
            if (isset($item['room_id'])) {
                $room = Room::with(['images', 'discounts'])->find($item['room_id']);
                if ($room) {
                    $room->cart_data = $item;
                    $cartRooms->push($room);
                }
            } elseif (isset($item['boat_id'])) {
                $boat = Boat::find($item['boat_id']);
                if ($boat) {
                    $boat->cart_data = $item;
                    $cartBoats->push($boat);
                }
            }
        }

        // Calculate totals
        $total = 0;
        foreach ($cart as $item) {
            if (isset($item['room_id'])) {
                $total += $item['line_total'] ?? (($item['unit_price'] ?? 0) * ($item['nights'] ?? 1));
            } elseif (isset($item['boat_id'])) {
                $total += $item['price'] ?? 0;
            }
        }

        $depositPercent = (float) Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
        $deposit = round($total * ($depositPercent / 100), 2);

        return view('home.cart.index', compact('cartRooms', 'cartBoats', 'total', 'deposit', 'depositPercent'));
    }
}
