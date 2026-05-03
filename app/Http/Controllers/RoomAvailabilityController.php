<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class RoomAvailabilityController extends Controller
{
    /**
     * GET|POST /check-room-availability
     *
     * Returns:
     *  { available: true }
     *  { available: false, message: string, blocked_dates: ['Y-m-d', ...],
     *    blocked_ranges: [{ start: 'Y-m-d', end: 'Y-m-d', label: string }, ...] }
     */
    public function check(Request $request)
    {
        $roomId    = $request->input('room_id');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        if (!$roomId || !$startDate || !$endDate) {
            return response()->json(['available' => false, 'message' => 'Missing required parameters.'], 400);
        }

        $startTime = $request->input('start_time') ?: '13:00';
        $endTime   = $request->input('end_time')   ?: '11:00';

        try {
            $requestedStart = Carbon::parse($startDate . ' ' . $startTime);
            $requestedEnd   = Carbon::parse($endDate   . ' ' . $endTime);
        } catch (\Exception $e) {
            return response()->json(['available' => false, 'message' => 'Invalid date/time format.'], 400);
        }

        if ($requestedEnd->lte($requestedStart)) {
            return response()->json(['available' => false, 'message' => 'Check-out must be after check-in.'], 400);
        }

        // Fetch all active conflicting bookings for this room in the requested range
        $conflicts = Booking::availableBetween($startDate, $endDate)
            ->where('room_id', $roomId)
            ->get(['start_date', 'end_date', 'status']);

        if ($conflicts->isEmpty()) {
            return response()->json(['available' => true]);
        }

        // Build blocked_dates: every individual date covered by any conflicting booking
        $blockedSet    = [];
        $blockedRanges = [];

        foreach ($conflicts as $booking) {
            $bStart = Carbon::parse($booking->start_date)->startOfDay();
            $bEnd   = Carbon::parse($booking->end_date)->startOfDay();

            // Clamp to the requested range so we only show relevant blocked days
            $rangeStart = $bStart->lt(Carbon::parse($startDate)) ? Carbon::parse($startDate) : $bStart->copy();
            $rangeEnd   = $bEnd->gt(Carbon::parse($endDate))     ? Carbon::parse($endDate)   : $bEnd->copy();

            // Collect every individual date in this booking's range
            $period = CarbonPeriod::create($rangeStart, $rangeEnd);
            foreach ($period as $day) {
                $blockedSet[$day->toDateString()] = true;
            }

            $blockedRanges[] = [
                'start' => $bStart->toDateString(),
                'end'   => $bEnd->toDateString(),
                'label' => 'Booked ' . $bStart->format('M j') . ' – ' . $bEnd->format('M j, Y'),
            ];
        }

        $blockedDates = array_keys($blockedSet);
        sort($blockedDates);

        // Build a human-readable summary
        $rangeCount = count($blockedRanges);
        $message    = $rangeCount === 1
            ? 'This room is booked from ' . $blockedRanges[0]['start'] . ' to ' . $blockedRanges[0]['end'] . '.'
            : 'This room has ' . $rangeCount . ' conflicting bookings in your selected range.';

        return response()->json([
            'available'      => false,
            'message'        => $message,
            'blocked_dates'  => $blockedDates,
            'blocked_ranges' => $blockedRanges,
        ]);
    }
}