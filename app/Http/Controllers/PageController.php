<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Boat;
use App\Models\Booking;
use App\Models\Setting;
class PageController extends Controller
{
    // page controllers
    public function home_contact()
    {
        return view('home.contact');
    }

    public function home_rooms()
    {
        $rooms = Room::with(['images', 'discounts.images'])->get();
        return view('home.rooms', compact('rooms'));
    }

    public function home_amenities()
    {
        return view('home.amenities');
    }

    public function home_alerts()
    {
        return view('home.alert-status');
    }

    public function home_boat()
    {
        $boats = Boat::all();
        return view('home.boats', compact('boats'));
    }


    public function home_roomcart(Request $request)
    {
        $rooms = Room::with(['images', 'discounts.images'])->get();
        $boats = Boat::all();

        $cart = session('cart', []);

        // Validate session cart rooms against existing bookings and remove overlaps.
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
                    continue; // skip adding this item
                }
            }

            $filtered[] = $item;
        }

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

        // Get search parameters from hero forms
        $searchParams = [
            'type' => $request->input('type', 'room'), // 'room' or 'boat'
            // Room search params
            'checkin' => $request->input('checkin'),
            'checkout' => $request->input('checkout'),
            'checkin_time' => $request->input('checkin_time'),
            'checkout_time' => $request->input('checkout_time'),
            'guests' => $request->input('guests', 1),
            // Boat search params
            'departure_date' => $request->input('departure_date'),
            'duration' => $request->input('duration'),
            'passengers' => $request->input('passengers', 1),
        ];

        return view('home.roomcart', compact('rooms', 'boats', 'cartRooms', 'cartBoats', 'searchParams'));
    }

    /**
     * Show authenticated user's bookings so they can request extensions.
     */
    public function home_bookings()
    {
        $email = auth()->user()->email ?? null;
        $bookings = [];
        if ($email) {
            $bookings = Booking::with('room', 'extensions')
                ->where('email', $email)
                ->orderBy('start_date', 'desc')
                ->get();
        }
        return view('home.bookings', compact('bookings'));
    }

    /**
     * Show the dedicated My Bookings page.
     */
    public function my_bookings_page()
    {
        return view('home.my-bookings');
    }

    /**
     * Return authenticated user's bookings as JSON for the My Bookings page.
     */
    public function api_my_bookings()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $depositPercent = (float) Setting::get('deposit_percentage', config('booking.deposit_percentage', 50)) / 100;
        // Use frontdesk-configured refund fee when computing displayed totals (deposit fee usually 0)
        $depositFeePercent = (float) Setting::get('deposit_fee_percentage', config('booking.deposit_fee_percentage', 0)) / 100;

        $bookings = Booking::with('room', 'extensions')
            ->where('email', $user->email)
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($b) use ($depositPercent, $depositFeePercent) {
                $total = (float) ($b->total_amount ?? 0);
                // Use stored deposit_amount if present, otherwise compute from config percent
                $deposit = $b->deposit_amount ?? round($total * $depositPercent, 2);
                $depositFee = $b->deposit_fee ?? round($deposit * $depositFeePercent, 2);
                $totalToCharge = $b->total_to_charge ?? round($deposit + $depositFee, 2);

                // compute group-level totals and flags
                $groupId = $b->group_id;
                $groupBookings = Booking::where('group_id', $groupId)->get();
                $groupDepositTotal = $groupBookings->reduce(function ($carry, $item) use ($depositPercent, $depositFeePercent) {
                    $t = (float) ($item->total_amount ?? 0);
                    $d = $item->deposit_amount ?? round($t * $depositPercent, 2);
                    return $carry + $d;
                }, 0);

                $groupHasRefund = $groupBookings->contains(function ($item) {
                    return in_array(strtolower($item->refund_status ?? ''), ['requested', 'processing', 'refunded']);
                });

                return [
                    'id' => $b->id,
                    'room_name' => optional($b->room)->room_name,
                    'start_date' => $b->start_date,
                    'end_date' => $b->end_date,
                    'checkin_date' => $b->start_date, // For compatibility
                    'checkout_date' => $b->end_date, // For compatibility
                    'checkin_time' => $b->start_time,
                    'checkout_time' => $b->end_time,
                    'scheduled_checkin_at' => $b->scheduled_checkin_at ? $b->scheduled_checkin_at->toDateTimeString() : null,
                    'scheduled_checkout_at' => $b->scheduled_checkout_at ? $b->scheduled_checkout_at->toDateTimeString() : null,
                    'actual_checkin_at' => $b->actual_checkin_at ? $b->actual_checkin_at->toDateTimeString() : null,
                    'actual_checkout_at' => $b->actual_checkout_at ? $b->actual_checkout_at->toDateTimeString() : null,
                    'nights' => $b->nights,
                    'adults' => $b->adults,
                    'children' => $b->children,
                    'status' => $b->status,
                    'total_amount' => $b->total_amount,
                    'total_price' => $b->total_amount, // For compatibility
                    'created_at' => $b->created_at,
                    // deposit info (per-booking)
                    'deposit_amount' => $deposit,
                    'deposit_fee' => $depositFee,
                    'total_to_charge' => $totalToCharge,
                    // group-level deposit + flag
                    'group_deposit_total' => round($groupDepositTotal, 2),
                    'group_has_refund' => $groupHasRefund,
                    'paid_amount' => $b->paid_amount,
                    // refund-related fields for client UI
                    'refund_status' => $b->refund_status,
                    'refund_requested_amount' => $b->refund_requested_amount,
                    'refund_fee' => $b->refund_fee,
                    'refund_amount' => $b->refund_amount,
                    'extensions' => $b->extensions->map(function ($e) {
                        return [
                            'id' => $e->id,
                            'hours' => $e->hours,
                            'status' => $e->status,
                            'price' => $e->price,
                        ];
                    }),
                    // front-end settings for refund form
                    'include_refund_fee_in_form' => (int) Setting::get('include_refund_fee_in_form', 0),
                    'refund_fee_percentage' => (float) Setting::get('refund_fee_percentage', 5),
                ];
            });

        // Calculate stats
        $stats = [
            'total' => $bookings->count(),
            'confirmed' => $bookings->where('status', 'confirmed')->count(),
            'upcoming' => $bookings->whereIn('status', ['confirmed', 'pending'])
                ->where('start_date', '>=', now()->toDateString())->count(),
        ];

        return response()->json([
            'bookings' => $bookings,
            'stats' => $stats
        ]);
    }



}
