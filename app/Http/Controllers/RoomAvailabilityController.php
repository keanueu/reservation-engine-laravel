<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
class RoomAvailabilityController extends Controller
{
    public function check(Request $request)
    {
        $roomId = $request->input('room_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if (!$roomId || !$startDate || !$endDate) {
            return response()->json(['available' => false, 'message' => 'Missing required parameters.'], 400);
        }

        // Optional time inputs (HH:MM). If not provided, use business defaults:
        // check-in default: 13:00 (1:00 PM), check-out default: 11:00 (11:00 AM)
        $startTime = $request->input('start_time') ?: '13:00';
        $endTime = $request->input('end_time') ?: '11:00';

        try {
            $requestedStart = Carbon::parse($startDate . ' ' . $startTime);
            $requestedEnd = Carbon::parse($endDate . ' ' . $endTime);
        } catch (\Exception $e) {
            return response()->json(['available' => false, 'message' => 'Invalid date/time format.'], 400);
        }

        // Find candidate bookings that overlap the requested date range (coarse filter)
        // Only consider paid bookings - exclude pending/waiting/cancelled/rejected
        $candidates = Booking::where('room_id', $roomId)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->where('payment_status', 'paid')
            ->whereNotIn('status', ['cancelled', 'rejected', 'waiting'])
            ->get();

        // For each candidate, compute the effective occupied interval using actual times
        foreach ($candidates as $b) {
            // booking scheduled datetimes (fallback to start_date/end_date with defaults)
            $schedStart = $b->scheduled_checkin_at ? Carbon::parse($b->scheduled_checkin_at) : Carbon::parse($b->start_date . ' 13:00');
            $schedEnd = $b->scheduled_checkout_at ? Carbon::parse($b->scheduled_checkout_at) : Carbon::parse($b->end_date . ' 11:00');

            // actual times supersede scheduled when present
            $bookingStart = $b->actual_checkin_at ? Carbon::parse($b->actual_checkin_at) : $schedStart;
            $bookingEnd = $b->actual_checkout_at ? Carbon::parse($b->actual_checkout_at) : $schedEnd;

            // If booking interval overlaps requested interval -> not available
            // Treat checkout moment as still occupying the room for an extra minute
            // so a new booking starting exactly at the existing checkout time is considered overlapping.
            if (!($bookingEnd->lt($requestedStart) || $bookingStart->gte($requestedEnd))) {
                return response()->json(['available' => false, 'message' => 'Room is already booked for the selected dates/times.']);
            }
        }

        return response()->json(['available' => true]);
    }
}