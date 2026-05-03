<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class HeroSearchController extends Controller
{
    public function searchStay(Request $request)
    {
        $request->validate([
            'checkin'       => 'required|date|after_or_equal:today',
            'checkin_time'  => 'required',
            'checkout'      => 'required|date|after:checkin',
            'checkout_time' => 'required',
            'guests'        => 'required|integer|min:1',
        ]);

        // Store guest count in session for step 2 pre-fill
        session(['hero_search_guests' => (int) $request->guests]);

        return redirect()->route('booking.dates', [
            'type'          => 'room',
            'checkin'       => $request->checkin,
            'checkout'      => $request->checkout,
            'checkin_time'  => $request->checkin_time,
            'checkout_time' => $request->checkout_time,
            'guests'        => $request->guests,
        ]);
    }

    public function searchSail(Request $request)
    {
        $request->validate([
            'departure_date' => 'required|date|after_or_equal:today',
            'duration'       => 'required|in:half,full,overnight',
            'passengers'     => 'required|integer|min:1',
        ]);

        // Convert duration to start/end times
        $duration = $request->duration;
        $startTime = '08:00';
        $endTime = '12:00';

        if ($duration === 'full') {
            $startTime = '08:00';
            $endTime = '16:00';
        } elseif ($duration === 'overnight') {
            $startTime = '14:00';
            $endTime = '10:00'; // Next day
        }

        // Store passenger count in session for step 2 pre-fill
        session(['hero_search_passengers' => (int) $request->passengers]);

        return redirect()->route('booking.dates', [
            'type'         => 'boat',
            'booking_date' => $request->departure_date,
            'start_time'   => $startTime,
            'end_time'     => $endTime,
            'passengers'   => $request->passengers,
        ]);
    }
}
