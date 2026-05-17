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

        // 1. Fetch arrivals with specific database columns and relationships
        $todaysArrivals = Booking::select('id', 'room_id', 'name', 'email', 'phone', 'start_date', 'end_date', 'status', 'payment_status', 'total_amount')
            ->with(['room' => function ($q) { $q->select('id', 'room_name'); }])
            ->forDate($today, 'start_date')
            ->whereIn('status', ['approve', 'confirmed'])
            ->get();

        // 2. Fetch departures with specific columns
        $todaysDepartures = Booking::select('id', 'room_id', 'name', 'email', 'phone', 'start_date', 'end_date', 'status', 'payment_status', 'total_amount', 'updated_at')
            ->with(['room' => function ($q) { $q->select('id', 'room_name'); }])
            ->where(function ($q) use ($today) {
                $q->whereDate('end_date', $today)
                  ->whereIn('status', ['checked-in', 'checked-out']);
            })
            ->orWhere(function ($q) use ($today) {
                $q->withStatus('checked-out')->whereDate('updated_at', $today);
            })
            ->get();

        // 3. Fetch in-house guests with specific columns
        $inHouseGuests = Booking::select('id', 'room_id', 'name', 'email', 'phone', 'start_date', 'end_date', 'status', 'payment_status', 'total_amount')
            ->with(['room' => function ($q) { $q->select('id', 'room_name'); }])
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
            'rooms'                => Room::select('id', 'room_name', 'room_type', 'price', 'accommodates', 'beds')->get(),
            'boats'                => Boat::select('id', 'name', 'capacity', 'price', 'image')->get(),
            'bookings'             => Booking::select('id', 'room_id', 'name', 'email', 'phone', 'start_date', 'end_date', 'status', 'payment_status', 'total_amount', 'paid_amount', 'refund_status')
                                        ->with(['room' => function ($q) { $q->select('id', 'room_name'); }])
                                        ->get(),
            'boatBookings'         => BoatBooking::select('id', 'boat_id', 'name', 'email', 'phone', 'booking_date', 'start_time', 'end_time', 'guests', 'status', 'payment_status', 'total_amount')
                                        ->with(['boat' => function ($q) { $q->select('id', 'name'); }])
                                        ->get(),
            'images'               => Images::select('id', 'image', 'room_id')->get(),
            'todaysArrivals'       => $todaysArrivals,
            'todaysDepartures'     => $todaysDepartures,
            'inHouseGuests'        => $inHouseGuests,
            'todaysBoatTrips'      => BoatBooking::select('id', 'boat_id', 'name', 'email', 'phone', 'booking_date', 'start_time', 'end_time', 'guests', 'status', 'payment_status', 'total_amount')
                                        ->with(['boat' => function ($q) { $q->select('id', 'name'); }])
                                        ->forDate($today, 'booking_date')
                                        ->get(),
        ]);
    }
}
