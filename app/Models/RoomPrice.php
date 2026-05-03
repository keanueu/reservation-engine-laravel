<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class RoomPrice extends Model
{
    protected $fillable = [
        'room_id',
        'start_date',
        'end_date',
        'price',
        'rate_type',
        'label',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'price'      => 'float',
        'is_active'  => 'boolean',
        'priority'   => 'integer',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // -------------------------------------------------------------------------
    // Pricing Engine
    // -------------------------------------------------------------------------

    /**
     * Calculate the total price for a room over a date range.
     *
     * Strategy (per night):
     *  1. Find all active RoomPrice rules that overlap the stay.
     *  2. For each night, pick the highest-priority matching rule.
     *  3. If no rule matches a night, fall back to the room's base price.
     *  4. Weekend surcharge is applied on top of any matched rule if rate_type = 'weekend'.
     *
     * Returns an array:
     *  [
     *    'total'      => float,   // grand total for all nights
     *    'nights'     => int,
     *    'base_price' => float,   // room's base price/night
     *    'breakdown'  => [        // per-night detail
     *      ['date' => 'Y-m-d', 'price' => float, 'label' => string, 'rate_type' => string],
     *      ...
     *    ],
     *    'has_dynamic_pricing' => bool,
     *    'applied_rules' => [     // unique rules that were applied
     *      ['label' => string, 'rate_type' => string, 'price' => float],
     *    ],
     *  ]
     */
    public static function calculateForRoom(
        Room   $room,
        string $checkin,
        string $checkout
    ): array {
        $checkinDate  = Carbon::parse($checkin)->startOfDay();
        $checkoutDate = Carbon::parse($checkout)->startOfDay();
        $basePrice    = (float) $room->price;

        // Nights = number of days the guest occupies the room
        // (checkout day is NOT a night — standard hotel convention)
        $nights = max(1, $checkinDate->diffInDays($checkoutDate));

        // Load all active rules that overlap this date range for this room
        $rules = self::where('room_id', $room->id)
            ->where('is_active', true)
            ->where('start_date', '<=', $checkoutDate->toDateString())
            ->where('end_date',   '>=', $checkinDate->toDateString())
            ->orderByDesc('priority')
            ->get();

        // Also load weekend rules (they apply on Sat/Sun regardless of date range)
        $weekendRules = self::where('room_id', $room->id)
            ->where('is_active', true)
            ->where('rate_type', 'weekend')
            ->orderByDesc('priority')
            ->get();

        $breakdown       = [];
        $total           = 0.0;
        $appliedRuleKeys = [];

        // Iterate each night (from checkin up to but not including checkout)
        $period = CarbonPeriod::create($checkinDate, $checkoutDate->copy()->subDay());

        foreach ($period as $night) {
            $dateStr    = $night->toDateString();
            $isWeekend  = $night->isWeekend(); // Sat or Sun

            // Find the best matching seasonal/holiday/promo rule for this night
            $matchedRule = null;
            foreach ($rules as $rule) {
                if ($night->between($rule->start_date, $rule->end_date)) {
                    if ($rule->rate_type !== 'weekend') {
                        $matchedRule = $rule;
                        break; // already sorted by priority desc
                    }
                }
            }

            // If it's a weekend, check if a weekend rule beats the matched rule
            if ($isWeekend) {
                $weekendRule = $weekendRules->first(
                    fn($r) => $night->between($r->start_date, $r->end_date)
                ) ?? $weekendRules->first(); // global weekend rule (no date range)

                if ($weekendRule) {
                    // Weekend rule wins if it has higher priority or no seasonal rule matched
                    if (!$matchedRule || $weekendRule->priority >= $matchedRule->priority) {
                        $matchedRule = $weekendRule;
                    }
                }
            }

            $nightPrice = $matchedRule ? $matchedRule->price : $basePrice;
            $label      = $matchedRule ? ($matchedRule->label ?? ucfirst($matchedRule->rate_type) . ' rate') : 'Base rate';
            $rateType   = $matchedRule ? $matchedRule->rate_type : 'base';

            $total += $nightPrice;
            $breakdown[] = [
                'date'      => $dateStr,
                'price'     => $nightPrice,
                'label'     => $label,
                'rate_type' => $rateType,
            ];

            // Track unique applied rules for the summary
            if ($matchedRule && !isset($appliedRuleKeys[$matchedRule->id])) {
                $appliedRuleKeys[$matchedRule->id] = [
                    'label'     => $label,
                    'rate_type' => $rateType,
                    'price'     => $matchedRule->price,
                ];
            }
        }

        return [
            'total'               => round($total, 2),
            'nights'              => $nights,
            'base_price'          => $basePrice,
            'breakdown'           => $breakdown,
            'has_dynamic_pricing' => !empty($appliedRuleKeys),
            'applied_rules'       => array_values($appliedRuleKeys),
        ];
    }
}
