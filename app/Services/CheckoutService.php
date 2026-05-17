<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BoatBooking;
use App\Models\Room;
use App\Models\Boat;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\DTOs\CheckoutPayloadDTO;

class CheckoutService
{
    /**
     * Process checkout in an atomic transaction.
     * Returns an array with ['groupId', 'totalToCharge', 'firstBookingId', 'bookings'].
     * Throws \RuntimeException on validation or availability failure.
     */
    public function processCheckout(CheckoutPayloadDTO $payload): array
    {
        $groupId  = Str::uuid()->toString();
        $total    = 0.0;
        $bookings = [];

        DB::transaction(function () use ($payload, $groupId, &$total, &$bookings) {
            
            // PHASE A: Lock ALL room rows first (sorted by ID to prevent deadlocks)
            $sortedRoomItems = collect($payload->roomItems)->sortBy('room_id');
            foreach ($sortedRoomItems as $cartItem) {
                $roomId    = $cartItem['room_id'];
                $startDate = $cartItem['start_date'] ?? null;
                $endDate   = $cartItem['end_date']   ?? null;

                if (!$startDate || !$endDate) {
                    throw new \RuntimeException('Missing dates for one of the rooms in your cart.');
                }

                $room = Room::lockForUpdate()->find($roomId);
                if (!$room) {
                    throw new \RuntimeException('A room in your cart no longer exists.');
                }

                $adults    = (int) ($cartItem['adults'] ?? 1);
                $children  = (int) ($cartItem['children'] ?? 0);
                $maxGuests = (int) $room->accommodates;

                if (($adults + $children) > $maxGuests) {
                    throw new \RuntimeException(sprintf('Guest count for "%s" exceeds capacity (%d).', $room->room_name, $maxGuests));
                }

                // Atomic availability check
                $conflict = Booking::availableBetween($startDate, $endDate)
                    ->where('room_id', $room->id)
                    ->lockForUpdate()
                    ->exists();

                if ($conflict) {
                    throw new \RuntimeException(sprintf('Room "%s" is no longer available for %s – %s. Please choose different dates.', $room->room_name, $startDate, $endDate));
                }

                $nights   = (int) ($cartItem['nights'] ?? 1);
                $subtotal = (float) ($cartItem['line_total'] ?? (($cartItem['unit_price'] ?? $room->price) * $nights));
                $total   += $subtotal;

                $scheduledCheckin  = $cartItem['scheduled_checkin_at']  ? Carbon::parse($cartItem['scheduled_checkin_at'])  : Carbon::parse($startDate . ' ' . ($cartItem['start_time'] ?? '13:00'));
                $scheduledCheckout = $cartItem['scheduled_checkout_at'] ? Carbon::parse($cartItem['scheduled_checkout_at']) : Carbon::parse($endDate   . ' ' . ($cartItem['end_time']   ?? '11:00'));
                
                $appliedDiscount = $room->discounts->first() ?? null;

                $bookings[] = Booking::create([
                    'group_id'              => $groupId,
                    'room_id'               => $room->id,
                    'name'                  => $payload->name,
                    'email'                 => $payload->email,
                    'phone'                 => $payload->phone,
                    'adults'                => $adults,
                    'children'              => $children,
                    'start_date'            => $startDate,
                    'end_date'              => $endDate,
                    'scheduled_checkin_at'  => $scheduledCheckin,
                    'scheduled_checkout_at' => $scheduledCheckout,
                    'nights'                => $nights,
                    'status'                => 'waiting',
                    'payment_status'        => 'pending',
                    'total_amount'          => $subtotal,
                    'promo_label'           => optional($appliedDiscount)->name,
                    'discount_id'           => optional($appliedDiscount)->id,
                    'expires_at'            => now()->addMinutes(Booking::PENDING_TTL_MINUTES),
                ]);
            }

            // PHASE B: Lock ALL boat rows
            $sortedBoatItems = collect($payload->boatItems)->sortBy('boat_id');
            foreach ($sortedBoatItems as $cartItem) {
                $boatId      = $cartItem['boat_id'];
                $bookingDate = $cartItem['booking_date'] ?? null;
                $startTime   = $cartItem['start_time']   ?? null;
                $endTime     = $cartItem['end_time']     ?? null;

                $boat = Boat::lockForUpdate()->find($boatId);
                if (!$boat) {
                    throw new \RuntimeException('A boat in your cart no longer exists.');
                }

                $boatConflict = BoatBooking::where('boat_id', $boatId)
                    ->where('booking_date', $bookingDate)
                    ->whereNotIn('status', ['cancelled', 'rejected'])
                    ->where(fn($q) => $q->where('start_time', '<', $endTime)->where('end_time', '>', $startTime))
                    ->lockForUpdate()
                    ->exists();

                if ($boatConflict) {
                    throw new \RuntimeException(sprintf('Boat "%s" is not available for the selected time slot.', $boat->name));
                }

                $boatPrice = (float) ($cartItem['price'] ?? $boat->price);
                $total    += $boatPrice;

                $bookings[] = BoatBooking::create([
                    'group_id'       => $groupId,
                    'boat_id'        => $boatId,
                    'name'           => $payload->name,
                    'email'          => $payload->email,
                    'phone'          => $payload->phone,
                    'booking_date'   => $bookingDate,
                    'start_time'     => $startTime,
                    'end_time'       => $endTime,
                    'guests'         => $cartItem['guests'] ?? 1,
                    'status'         => 'waiting',
                    'payment_status' => 'pending',
                    'total_amount'   => $boatPrice,
                ]);
            }

            // PHASE C: Minimum amount guard
            if ($total < 100) {
                throw new \RuntimeException('Minimum total is ₱100. Please add more items.');
            }

            // PHASE D: Persist deposit fields
            $depositPercent    = (float) \App\Models\Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
            $depositFeePercent = (float) \App\Models\Setting::get('deposit_fee_percentage', config('booking.deposit_fee_percentage', 0));

            foreach ($bookings as $b) {
                $ba  = (float) ($b->total_amount ?? 0);
                $dep = round($ba * ($depositPercent / 100), 2);
                $fee = round($dep * ($depositFeePercent / 100), 2);

                $b->deposit_amount  = $dep;
                $b->deposit_fee     = $fee;
                $b->total_to_charge = round($dep + $fee, 2);
                $b->save();
            }
        });

        // Calculate total to charge
        $depositPercent    = (float) \App\Models\Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
        $depositFeePercent = (float) \App\Models\Setting::get('deposit_fee_percentage', config('booking.deposit_fee_percentage', 0));
        $depositAmount     = $total * ($depositPercent / 100);
        $depositFee        = $depositAmount * ($depositFeePercent / 100);
        $totalToCharge     = round($depositAmount + $depositFee, 2);

        return [
            'groupId'       => $groupId,
            'totalToCharge' => $totalToCharge,
            'firstBookingId'=> $bookings[0]->id ?? null,
            'bookings'      => $bookings,
        ];
    }
}
