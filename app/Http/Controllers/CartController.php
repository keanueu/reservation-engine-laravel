<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Boat;
use App\Models\Booking;
use App\Models\BoatBooking;
use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class CartController extends Controller
{
    public function add(Request $request, $room_id)
    {
        $room = Room::findOrFail($room_id);
        $cart = session('cart', []);

        // get guest data here
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $adults = (int) $request->input('adults');
        $children = (int) $request->input('children');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        $totalGuests = $adults + $children;
        $maxGuests = (int) $room->accommodates;

        if ($totalGuests > $maxGuests) {
            return response()->json([
                'status' => 'error',
                'message' => 'Guest count exceeds room accommodates. Please adjust guest count.'
            ], 422);
        }

        // Server-side: require times to be provided (client enforces this too)
        if (empty($start_time) || empty($end_time)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please provide both check-in and check-out times.'
            ], 422);
        }

        // calculate stay duration
        $nights = 1;
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $nights = $start->diffInDays($end);
            if ($nights < 1)
                $nights = 1;

            \Log::info('CartController@add-debug', [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'start_parsed' => $start->toDateString(),
                'end_parsed' => $end->toDateString(),
                'diffInDays' => $nights
            ]);
        }

        \Log::info('CartController@add', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'nights' => $nights
        ]);

        // --- Determine discounted unit price for this room (per night) ---
        $discount = $room->discounts->first() ?? null;
        $discountValue = optional($discount)->amount ?? 0;
        $isPercentage = optional($discount)->amount_type === 'percent' || optional($discount)->amount_type === 'percentage';
        $isFixedAmount = optional($discount)->amount_type === 'fixed';
        $isActive = optional($discount)->active ?? false;

        $unitPrice = $room->price;
        if ($isActive && $discountValue > 0) {
            if ($isPercentage) {
                $unitPrice = $room->price * (1 - ($discountValue / 100));
            } elseif ($isFixedAmount) {
                $unitPrice = max(0, $room->price - $discountValue);
            }
        }

        // update or add room in cart with new data
        $updated = false;
        foreach ($cart as &$item) {
            if (
                isset($item['room_id'], $item['start_date'], $item['end_date']) &&
                $item['room_id'] == $room->id &&
                $item['start_date'] == $startDate &&
                $item['end_date'] == $endDate
            ) {
                $item['adults'] = $adults;
                $item['children'] = $children;
                $item['nights'] = $nights;
                // update unit price and line total if nights changed or discount changed
                $item['unit_price'] = $unitPrice;
                $item['original_unit_price'] = $room->price;
                $item['discount'] = $discountValue;
                $item['discount_type'] = optional($discount)->amount_type ?? null;
                $item['line_total'] = ($item['unit_price'] ?? $room->price) * ($item['nights'] ?? $nights);
                // persist optional time fields
                $item['start_time'] = $start_time;
                $item['end_time'] = $end_time;
                $item['scheduled_checkin_at'] = $startDate ? Carbon::parse($startDate . ' ' . ($start_time ?: '13:00'))->toDateTimeString() : null;
                $item['scheduled_checkout_at'] = $endDate ? Carbon::parse($endDate . ' ' . ($end_time ?: '11:00'))->toDateTimeString() : null;
                $updated = true;
                break;
            }
        }
        unset($item); // avoid reference bugs

        // Add new room entry if not updated
        if (!$updated) {
            $cart[] = [
                'type' => 'room',
                'room_id' => $room->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'scheduled_checkin_at' => $startDate ? Carbon::parse($startDate . ' ' . ($start_time ?: '13:00'))->toDateTimeString() : null,
                'scheduled_checkout_at' => $endDate ? Carbon::parse($endDate . ' ' . ($end_time ?: '11:00'))->toDateTimeString() : null,
                'adults' => $adults,
                'children' => $children,
                'nights' => $nights,
                // pricing fields
                'original_unit_price' => $room->price,
                'unit_price' => $unitPrice,
                'discount' => $discountValue,
                'discount_type' => optional($discount)->amount_type ?? null,
                'line_total' => $unitPrice * $nights,
            ];
        }

        session(['cart' => $cart]);

        // build room and boat data for rendering
        $cartRooms = collect();
        $cartBoats = collect();
        foreach ($cart as $item) {
            if (isset($item['room_id'])) {
                $roomModel = Room::find($item['room_id']);
                if ($roomModel) {
                    $roomModel->cart_data = $item;
                    $cartRooms->push($roomModel);
                }
            } elseif (isset($item['boat_id'])) {
                $boatModel = Boat::find($item['boat_id']);
                if ($boatModel) {
                    $boatModel->cart_data = $item;
                    $cartBoats->push($boatModel);
                }
            }
        }

        // Compute totals based on cart items (respect discounts and nights)
        $total = 0;
        foreach ($cart as $item) {
            if (isset($item['room_id'])) {
                $total += $item['line_total'] ?? (($item['unit_price'] ?? 0) * ($item['nights'] ?? 1));
            } elseif (isset($item['boat_id'])) {
                $total += $item['price'] ?? 0;
            }
        }

        $depositPercent = (float) Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
        $deposit = $total * ($depositPercent / 100);

        $cartHtml = View::make('home.partials.cart-summary', [
            'cartRooms' => $cartRooms,
            'cartBoats' => $cartBoats,
            'total' => $total,
            'deposit' => $deposit,
        ])->render();

        return response()->json([
            'status' => 'success',
            'message' => 'Room added to cart successfully!',
            'cart_html' => $cartHtml
        ]);
    }

    public function remove($room_id)
    {
        $cart = session('cart', []);

        // Safely filter out the selected room
        $cart = array_filter($cart, function ($item) use ($room_id) {
            return !(isset($item['room_id']) && $item['room_id'] == $room_id);
        });

        session(['cart' => array_values($cart)]);

        // If the user previously created pending bookings (session) and then removes
        // this room from their cart, clean up any pending DB bookings that belong
        // to that same session group. Only remove bookings that are not paid.
        try {
            $pendingGroup = session('pending_booking_group');
            $pendingIds = session('pending_booking_ids', []);
            if ($pendingGroup) {
                // If this group contains the removed room, delete pending bookings in that group
                $hasRoom = Booking::where('group_id', $pendingGroup)
                    ->where('room_id', $room_id)
                    ->exists();

                if ($hasRoom) {
                    Booking::where('group_id', $pendingGroup)
                        ->where(function ($q) {
                            $q->where('payment_status', 'pending')
                                ->orWhereNull('payment_status')
                                ->orWhere('status', 'waiting');
                        })->delete();

                    // clear pending booking session info
                    session()->forget(['pending_booking_group', 'pending_booking_ids']);
                }
            }
        } catch (\Throwable $e) {
            // don't break removal if cleanup fails; just log it for later
            \Log::warning('Failed to cleanup pending bookings on cart remove: ' . $e->getMessage());
        }

        // Rebuild $cartRooms and $cartBoats safely
        $cartRooms = collect();
        $cartBoats = collect();
        foreach ($cart as $item) {
            if (isset($item['room_id'])) {
                $room = Room::find($item['room_id']);
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

        // Detect which partial to render
        $referer = request()->headers->get('referer', '');
        if (request()->hasHeader('X-From-Checkout') || str_contains($referer, '/home/checkout')) {
            $partial = 'home.partials.checkout-price-details';
        } elseif (request()->hasHeader('X-From-RoomCart') || str_contains($referer, '/home/roomcart')) {
            $partial = 'home.partials.cart-checkout';
        } else {
            $partial = 'home.partials.cart-summary';
        }

        // Compute totals based on session cart items to ensure discounts are respected
        $total = 0;
        $sessionCart = session('cart', []);
        foreach ($sessionCart as $citem) {
            if (isset($citem['room_id'])) {
                $total += $citem['line_total'] ?? (($citem['unit_price'] ?? 0) * ($citem['nights'] ?? 1));
            } elseif (isset($citem['boat_id'])) {
                $total += $citem['price'] ?? 0;
            }
        }
        $depositPercent = (float) Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
        $deposit = $total * ($depositPercent / 100);

        $cartHtml = View::make($partial, [
            'cartRooms' => $cartRooms,
            'cartBoats' => $cartBoats,
            'total' => $total,
            'deposit' => $deposit,
        ])->render();

        return response()->json([
            'status' => 'success',
            'cart_html' => $cartHtml
        ]);
    }

    // --- BOAT CART METHODS ---
    public function checkBoatAvailability(Request $request)
    {
        $boat_id = $request->input('boat_id');
        $booking_date = $request->input('booking_date');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        $guests = (int) $request->input('guests');

        $boat = Boat::find($boat_id);
        if (!$boat) {
            return response()->json(['available' => false, 'message' => 'Boat not found.'], 404);
        }
        if ($guests > $boat->capacity) {
            return response()->json(['available' => false, 'message' => 'Guest count exceeds boat capacity.'], 422);
        }
        // Check for overlapping bookings
        $overlap = BoatBooking::where('boat_id', $boat_id)
            ->where('booking_date', $booking_date)
            ->where(function ($q) use ($start_time, $end_time) {
                $q->where(function ($q2) use ($start_time, $end_time) {
                    $q2->where('start_time', '<', $end_time)
                        ->where('end_time', '>', $start_time);
                });
            })
            ->exists();
        if ($overlap) {
            return response()->json(['available' => false, 'message' => 'Boat is not available for the selected time.']);
        }
        return response()->json(['available' => true]);
    }

    public function addBoatToCart(Request $request, $boat_id)
    {
        $boat = Boat::find($boat_id);
        if (!$boat) {
            return response()->json(['status' => 'error', 'message' => 'Boat not found.'], 404);
        }
        $cart = session('cart', []);
        $booking_date = $request->input('booking_date');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        $guests = (int) $request->input('guests');

        // Validate guest count
        if ($guests > $boat->capacity) {
            return response()->json(['status' => 'error', 'message' => 'Guest count exceeds boat capacity.'], 422);
        }

        // Add boat to cart
        $cart[] = [
            'type' => 'boat',
            'boat_id' => $boat->id,
            'booking_date' => $booking_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'guests' => $guests,
            'price' => $boat->price,
        ];
        session(['cart' => $cart]);

        // Build cart data for rendering
        $cartRooms = collect();
        $cartBoats = collect();
        foreach ($cart as $item) {
            if (isset($item['room_id'])) {
                $roomModel = Room::find($item['room_id']);
                if ($roomModel) {
                    $roomModel->cart_data = $item;
                    $cartRooms->push($roomModel);
                }
            } elseif (isset($item['boat_id'])) {
                $boatModel = Boat::find($item['boat_id']);
                if ($boatModel) {
                    $boatModel->cart_data = $item;
                    $cartBoats->push($boatModel);
                }
            }
        }

        // Compute totals based on session cart items to ensure discounts are respected
        $total = 0;
        $sessionCart = session('cart', []);
        foreach ($sessionCart as $citem) {
            if (isset($citem['room_id'])) {
                $total += $citem['line_total'] ?? (($citem['unit_price'] ?? 0) * ($citem['nights'] ?? 1));
            } elseif (isset($citem['boat_id'])) {
                $total += $citem['price'] ?? 0;
            }
        }
        $depositPercent = (float) Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
        $deposit = $total * ($depositPercent / 100);

        $cartHtml = \View::make('home.partials.cart-summary', [
            'cartRooms' => $cartRooms,
            'cartBoats' => $cartBoats,
            'total' => $total,
            'deposit' => $deposit,
        ])->render();

        return response()->json([
            'status' => 'success',
            'cart_html' => $cartHtml
        ]);
    }

    public function removeBoatFromCart($boat_id)
    {
        $cart = session('cart', []);
        $cart = array_filter($cart, function ($item) use ($boat_id) {
            return !(isset($item['boat_id']) && $item['boat_id'] == $boat_id);
        });
        session(['cart' => array_values($cart)]);

        // Clean up pending boat bookings if they belong to a pending booking group
        try {
            $pendingGroup = session('pending_booking_group');
            if ($pendingGroup) {
                $hasBoat = BoatBooking::where('group_id', $pendingGroup)
                    ->where('boat_id', $boat_id)
                    ->exists();

                if ($hasBoat) {
                    BoatBooking::where('group_id', $pendingGroup)
                        ->where(function ($q) {
                            $q->where('payment_status', 'pending')
                                ->orWhereNull('payment_status')
                                ->orWhere('status', 'waiting');
                        })->delete();

                    session()->forget(['pending_booking_group', 'pending_booking_ids']);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Failed to cleanup pending boat bookings on cart remove: ' . $e->getMessage());
        }

        $cartRooms = collect();
        $cartBoats = collect();
        foreach ($cart as $item) {
            if (isset($item['room_id'])) {
                $room = \App\Models\Room::find($item['room_id']);
                if ($room) {
                    $room->cart_data = $item;
                    $cartRooms->push($room);
                }
            } elseif (isset($item['boat_id'])) {
                $boat = \App\Models\Boat::find($item['boat_id']);
                if ($boat) {
                    $boat->cart_data = $item;
                    $cartBoats->push($boat);
                }
            }
        }

        $referer = request()->headers->get('referer', '');
        if (request()->hasHeader('X-From-Checkout') || str_contains($referer, '/home/checkout')) {
            $partial = 'home.partials.checkout-price-details';
        } elseif (request()->hasHeader('X-From-RoomCart') || str_contains($referer, '/home/roomcart')) {
            $partial = 'home.partials.cart-checkout';
        } else {
            $partial = 'home.partials.cart-summary';
        }

        // Compute totals based on session cart items to ensure discounts are respected
        $total = 0;
        $sessionCart = session('cart', []);
        foreach ($sessionCart as $citem) {
            if (isset($citem['room_id'])) {
                $total += $citem['line_total'] ?? (($citem['unit_price'] ?? 0) * ($citem['nights'] ?? 1));
            } elseif (isset($citem['boat_id'])) {
                $total += $citem['price'] ?? 0;
            }
        }
        $depositPercent = (float) config('booking.deposit_percentage', 50);
        $deposit = $total * ($depositPercent / 100);

        $cartHtml = \View::make($partial, [
            'cartRooms' => $cartRooms,
            'cartBoats' => $cartBoats,
            'total' => $total,
            'deposit' => $deposit,
        ])->render();

        return response()->json([
            'status' => 'success',
            'cart_html' => $cartHtml
        ]);
    }
}
