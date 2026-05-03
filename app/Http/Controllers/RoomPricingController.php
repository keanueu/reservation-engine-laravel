<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomPrice;
use Carbon\Carbon;

class RoomPricingController extends Controller
{
    /**
     * GET /room-pricing?room_id=&checkin=&checkout=
     *
     * Returns dynamic pricing breakdown for the room detail widget.
     * Public route — no auth required.
     */
    public function calculate(Request $request)
    {
        $roomId   = $request->input('room_id');
        $checkin  = $request->input('checkin');
        $checkout = $request->input('checkout');

        if (!$roomId || !$checkin || !$checkout) {
            return response()->json(['error' => 'Missing parameters.'], 400);
        }

        try {
            $checkinDate  = Carbon::parse($checkin);
            $checkoutDate = Carbon::parse($checkout);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format.'], 400);
        }

        if ($checkoutDate->lte($checkinDate)) {
            return response()->json(['error' => 'Check-out must be after check-in.'], 400);
        }

        $room = Room::find($roomId);
        if (!$room) {
            return response()->json(['error' => 'Room not found.'], 404);
        }

        $pricing = RoomPrice::calculateForRoom($room, $checkin, $checkout);

        return response()->json([
            'room_id'             => $room->id,
            'base_price'          => $pricing['base_price'],
            'total'               => $pricing['total'],
            'nights'              => $pricing['nights'],
            'has_dynamic_pricing' => $pricing['has_dynamic_pricing'],
            'applied_rules'       => $pricing['applied_rules'],
            'breakdown'           => $pricing['breakdown'],
        ]);
    }
}
