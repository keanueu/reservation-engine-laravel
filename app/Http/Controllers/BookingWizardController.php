<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Boat;
use App\Models\Setting;
use Carbon\Carbon;

class BookingWizardController extends Controller
{
    // Step 1 — Dates & Selection
    public function stepDates(Request $request)
    {
        $rooms = Room::with(['images', 'discounts.images'])->get();
        $boats = Boat::all();

        // Pre-fill from query string (e.g. from "Book Now" buttons or hero search)
        $prefill = [
            'room_id'       => $request->query('room_id'),
            'boat_id'       => $request->query('boat_id'),
            'checkin'       => $request->query('checkin'),
            'checkout'      => $request->query('checkout'),
            'checkin_time'  => $request->query('checkin_time', '13:00'),
            'checkout_time' => $request->query('checkout_time', '11:00'),
            'type'          => $request->query('type', 'room'), // 'room' or 'boat'
            'guests'        => $request->query('guests', 1),
            'booking_date'  => $request->query('booking_date'),
            'start_time'    => $request->query('start_time'),
            'end_time'      => $request->query('end_time'),
            'passengers'    => $request->query('passengers', 1),
        ];

        // Merge with any existing wizard session so back-navigation restores values
        $wizard = session('booking_wizard', []);
        $prefill = array_merge($prefill, array_filter($wizard['step1'] ?? []));

        return view('home.booking.step-dates', compact('rooms', 'boats', 'prefill'));
    }

    // Step 1 POST — validate & store, redirect to step 2
    public function postDates(Request $request)
    {
        $type = $request->input('type', 'room');

        if ($type === 'boat') {
            $request->validate([
                'boat_id'      => 'required|exists:boats,id',
                'booking_date' => 'required|date|after_or_equal:today',
                'start_time'   => 'required',
                'end_time'     => 'required',
            ]);

            session(['booking_wizard' => [
                'step1' => [
                    'type'         => 'boat',
                    'boat_id'      => $request->boat_id,
                    'booking_date' => $request->booking_date,
                    'start_time'   => $request->start_time,
                    'end_time'     => $request->end_time,
                ],
            ]]);
        } else {
            $request->validate([
                'room_id'  => 'required|exists:rooms,id',
                'checkin'  => 'required|date|after_or_equal:today',
                'checkout' => 'required|date|after:checkin',
            ]);

            session(['booking_wizard' => [
                'step1' => [
                    'type'          => 'room',
                    'room_id'       => $request->room_id,
                    'checkin'       => $request->checkin,
                    'checkout'      => $request->checkout,
                    'checkin_time'  => $request->checkin_time  ?? '13:00',
                    'checkout_time' => $request->checkout_time ?? '11:00',
                    'nights'        => Carbon::parse($request->checkin)->diffInDays(Carbon::parse($request->checkout)) ?: 1,
                ],
            ]]);
        }

        return redirect()->route('booking.guests');
    }

    // Step 2 — Guests
    public function stepGuests()
    {
        $wizard = session('booking_wizard');
        if (empty($wizard['step1'])) {
            return redirect()->route('booking.dates');
        }

        $step1 = $wizard['step1'];
        $model = $step1['type'] === 'boat'
            ? Boat::findOrFail($step1['boat_id'])
            : Room::findOrFail($step1['room_id']);

        $maxGuests = $step1['type'] === 'boat'
            ? (int) $model->capacity
            : (int) $model->accommodates;

        // Pre-fill from wizard session or hero search session
        $prefill = $wizard['step2'] ?? [];
        
        if (empty($prefill)) {
            // Try to get from hero search session
            if ($step1['type'] === 'boat') {
                $passengers = session('hero_search_passengers', 1);
                $prefill = ['adults' => $passengers, 'children' => 0];
                session()->forget('hero_search_passengers');
            } else {
                $guests = session('hero_search_guests', 1);
                $prefill = ['adults' => $guests, 'children' => 0];
                session()->forget('hero_search_guests');
            }
        }
        
        if (empty($prefill)) {
            $prefill = ['adults' => 1, 'children' => 0];
        }

        return view('home.booking.step-guests', compact('step1', 'model', 'maxGuests', 'prefill'));
    }

    // Step 2 POST — validate & store, redirect to step 3
    public function postGuests(Request $request)
    {
        $wizard = session('booking_wizard');
        if (empty($wizard['step1'])) {
            return redirect()->route('booking.dates');
        }

        $request->validate([
            'adults'   => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
        ]);

        $wizard['step2'] = [
            'adults'   => (int) $request->adults,
            'children' => (int) $request->children,
        ];
        session(['booking_wizard' => $wizard]);

        return redirect()->route('booking.review');
    }

    // Step 3 — Review
    public function stepReview()
    {
        $wizard = session('booking_wizard');
        if (empty($wizard['step1']) || empty($wizard['step2'])) {
            return redirect()->route('booking.dates');
        }

        $step1 = $wizard['step1'];
        $step2 = $wizard['step2'];

        $model = $step1['type'] === 'boat'
            ? Boat::findOrFail($step1['boat_id'])
            : Room::with('discounts')->findOrFail($step1['room_id']);

        // Compute price
        if ($step1['type'] === 'boat') {
            $unitPrice = $model->price;
            $total     = $unitPrice;
            $nights    = null;
        } else {
            $discount  = $model->discounts->first();
            $unitPrice = $model->price;
            if ($discount && ($discount->active ?? false) && $discount->amount > 0) {
                $unitPrice = $discount->amount_type === 'percent'
                    ? $model->price * (1 - $discount->amount / 100)
                    : max(0, $model->price - $discount->amount);
            }
            $nights = $step1['nights'];
            $total  = $unitPrice * $nights;
        }

        $depositPercent = (float) Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
        $deposit        = round($total * ($depositPercent / 100), 2);

        return view('home.booking.step-review', compact(
            'step1', 'step2', 'model', 'unitPrice', 'total', 'nights', 'deposit', 'depositPercent'
        ));
    }

    // Step 3 POST — push to cart and redirect to cart page
    public function postReview(Request $request)
    {
        $wizard = session('booking_wizard');
        if (empty($wizard['step1']) || empty($wizard['step2'])) {
            return redirect()->route('booking.dates');
        }

        $step1 = $wizard['step1'];
        $step2 = $wizard['step2'];
        $cart  = session('cart', []);

        if ($step1['type'] === 'boat') {
            $boat = Boat::findOrFail($step1['boat_id']);
            $cart[] = [
                'type'         => 'boat',
                'boat_id'      => $boat->id,
                'booking_date' => $step1['booking_date'],
                'start_time'   => $step1['start_time'],
                'end_time'     => $step1['end_time'],
                'guests'       => $step2['adults'] + $step2['children'],
                'price'        => $boat->price,
            ];
        } else {
            $room     = Room::with('discounts')->findOrFail($step1['room_id']);
            $discount = $room->discounts->first();
            $unitPrice = $room->price;
            if ($discount && ($discount->active ?? false) && $discount->amount > 0) {
                $unitPrice = $discount->amount_type === 'percent'
                    ? $room->price * (1 - $discount->amount / 100)
                    : max(0, $room->price - $discount->amount);
            }
            $nights = $step1['nights'];

            $cart[] = [
                'type'                  => 'room',
                'room_id'               => $room->id,
                'start_date'            => $step1['checkin'],
                'end_date'              => $step1['checkout'],
                'start_time'            => $step1['checkin_time'],
                'end_time'              => $step1['checkout_time'],
                'scheduled_checkin_at'  => Carbon::parse($step1['checkin']  . ' ' . $step1['checkin_time'])->toDateTimeString(),
                'scheduled_checkout_at' => Carbon::parse($step1['checkout'] . ' ' . $step1['checkout_time'])->toDateTimeString(),
                'adults'                => $step2['adults'],
                'children'              => $step2['children'],
                'nights'                => $nights,
                'original_unit_price'   => $room->price,
                'unit_price'            => $unitPrice,
                'discount'              => optional($discount)->amount ?? 0,
                'discount_type'         => optional($discount)->amount_type ?? null,
                'line_total'            => $unitPrice * $nights,
            ];
        }

        session(['cart' => $cart]);
        session()->forget('booking_wizard');

        return redirect()->route('cart.show')->with('success', 'Added to cart! Review your selection below.');
    }
}
