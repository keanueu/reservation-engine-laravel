<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BoatBooking;
use App\Models\Room;
use App\Models\Boat;
use App\Models\Images;
use Carbon\Carbon;

class FrontdeskController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $todaysArrivals = Booking::with('room')
            ->forDate($today, 'start_date')
            ->whereIn('status', ['approve', 'confirmed'])
            ->get();

        $todaysDepartures = Booking::with('room')
            ->where(function ($q) use ($today) {
                $q->whereDate('end_date', $today)
                  ->whereIn('status', ['checked-in', 'checked-out']);
            })
            ->orWhere(function ($q) use ($today) {
                $q->withStatus('checked-out')->whereDate('updated_at', $today);
            })
            ->get();

        $inHouseGuests = Booking::with('room')
            ->withStatus('checked-in')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();

        $occupiedRoomIds  = $inHouseGuests->pluck('room_id')->all();
        $availableRoomsCount = Room::whereNotIn('id', $occupiedRoomIds)->count();

        return view('frontdesk.index', [
            'todaysCheckInsCount'  => $todaysArrivals->count(),
            'todaysCheckOutsCount' => $todaysDepartures->count(),
            'inHouseGuestsCount'   => $inHouseGuests->count(),
            'availableRoomsCount'  => $availableRoomsCount,
            'roomsCount'           => Room::count(),
            'boatsCount'           => Boat::count(),
            'bookingsCount'        => Booking::count(),
            'rooms'                => Room::all(),
            'boats'                => Boat::all(),
            'bookings'             => Booking::with('room')->get(),
            'boatBookings'         => BoatBooking::with('boat')->get(),
            'images'               => Images::all(),
            'todaysArrivals'       => $todaysArrivals,
            'todaysDepartures'     => $todaysDepartures,
            'inHouseGuests'        => $inHouseGuests,
            'todaysBoatTrips'      => BoatBooking::with('boat')->forDate($today, 'booking_date')->get(),
        ]);
    }
}
