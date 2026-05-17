<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Boat;
use App\Models\Booking;
use App\Models\BoatBooking;
use Carbon\Carbon;

class CartService
{
    /**
     * Validate adding a room to the cart.
     * Throws \RuntimeException on failure.
     */
    public function validateRoomAddition(Room $room, array $payload, array $currentCart): void
    {
        $adults    = (int) ($payload['adults'] ?? 1);
        $children  = (int) ($payload['children'] ?? 0);
        $startDate = $payload['startDate'] ?? null;
        $endDate   = $payload['endDate'] ?? null;
        $startTime = $payload['start_time'] ?? null;
        $endTime   = $payload['end_time'] ?? null;

        if (($adults + $children) > (int) $room->accommodates) {
            throw new \RuntimeException('Guest count exceeds room capacity. Please adjust.');
        }

        if (empty($startTime) || empty($endTime)) {
            throw new \RuntimeException('Please provide both check-in and check-out times.');
        }

        if ($startDate && $endDate) {
            $conflict = Booking::availableBetween($startDate, $endDate)
                ->where('room_id', $room->id)
                ->exists();

            if ($conflict) {
                throw new \RuntimeException('This room is already booked for the selected dates.');
            }
        }

        foreach ($currentCart as $item) {
            if (isset($item['room_id'], $item['start_date'], $item['end_date']) && $item['room_id'] == $room->id) {
                $existingStart = Carbon::parse($item['start_date']);
                $existingEnd   = Carbon::parse($item['end_date']);
                $newStart      = Carbon::parse($startDate);
                $newEnd        = Carbon::parse($endDate);

                if ($newStart < $existingEnd && $newEnd > $existingStart) {
                    throw new \RuntimeException('This room is already in your cart for these dates. Please remove the existing item from your cart if you wish to modify the booking.');
                }
            }
        }
    }

    /**
     * Validate adding a boat to the cart.
     * Throws \RuntimeException on failure.
     */
    public function validateBoatAddition(Boat $boat, array $payload): void
    {
        $guests = (int) ($payload['guests'] ?? 1);
        if ($guests > $boat->capacity) {
            throw new \RuntimeException('Guest count exceeds boat capacity.');
        }

        $bookingDate = $payload['booking_date'] ?? null;
        $startTime   = $payload['start_time'] ?? null;
        $endTime     = $payload['end_time'] ?? null;

        if ($bookingDate && $startTime && $endTime) {
            $overlap = BoatBooking::where('boat_id', $boat->id)
                ->where('booking_date', $bookingDate)
                ->whereNotIn('status', ['cancelled', 'rejected'])
                ->where(fn($q) => $q->where('start_time', '<', $endTime)->where('end_time', '>', $startTime))
                ->exists();

            if ($overlap) {
                throw new \RuntimeException('Boat is not available for the selected time.');
            }
        }
    }

    /**
     * Clean up pending room bookings from the database.
     */
    public function cleanupPendingRoomBookings(string $groupId, int|string $roomId): void
    {
        Booking::where('group_id', $groupId)
            ->where('room_id', $roomId)
            ->where(fn($q) => $q->where('payment_status', 'pending')
                ->orWhereNull('payment_status')
                ->orWhere('status', 'waiting'))
            ->delete();
    }

    /**
     * Clean up pending boat bookings from the database.
     */
    public function cleanupPendingBoatBookings(string $groupId, int|string $boatId): void
    {
        BoatBooking::where('group_id', $groupId)
            ->where('boat_id', $boatId)
            ->where(fn($q) => $q->where('payment_status', 'pending')
                ->orWhereNull('payment_status')
                ->orWhere('status', 'waiting'))
            ->delete();
    }
}
