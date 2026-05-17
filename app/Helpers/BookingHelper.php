<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Setting;
use App\Models\Room;
use App\Models\Boat;

class BookingHelper
{
    /**
     * Calculate the effective unit price considering active discounts.
     */
    public static function calculateUnitPrice(float $basePrice, ?object $discount): float
    {
        if (!$discount || !($discount->active ?? false) || $discount->amount <= 0) {
            return $basePrice;
        }

        return $discount->amount_type === 'percent'
            ? $basePrice * (1 - $discount->amount / 100)
            : max(0, $basePrice - $discount->amount);
    }

    /**
     * Calculate required deposit amount for a booking total.
     */
    public static function calculateDeposit(float $total): float
    {
        $depositPercent = (float) Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
        return round($total * ($depositPercent / 100), 2);
    }
}
