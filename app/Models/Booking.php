<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    // Statuses that block a room from being booked
    const BLOCKING_STATUSES = ['confirmed', 'waiting', 'checked_in'];

    // How long a pending/waiting booking holds a room before expiring
    const PENDING_TTL_MINUTES = 30;

    protected $fillable = [
        'group_id',
        'room_id',
        'name',
        'email',
        'phone',
        'start_date',
        'end_date',
        'adults',
        'children',
        'payment_id',
        'payment_status',
        'total_amount',
        'promo_label',
        'discount_id',
        'paid_amount',
        'paid_at',
        'deposit_amount',
        'deposit_fee',
        'total_to_charge',
        'refund_status',
        'refund_requested_amount',
        'refund_fee',
        'refund_amount',
        'refund_reason',
        'refunded_at',
        'nights',
        'scheduled_checkin_at',
        'scheduled_checkout_at',
        'actual_checkin_at',
        'actual_checkout_at',
        'expires_at',
        'warned_at',
    ];

    protected $casts = [
        'scheduled_checkin_at'  => 'datetime',
        'scheduled_checkout_at' => 'datetime',
        'actual_checkin_at'     => 'datetime',
        'actual_checkout_at'    => 'datetime',
        'paid_at'               => 'datetime',
        'expires_at'            => 'datetime',
        'warned_at'             => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function extensions()
    {
        return $this->hasMany(\App\Models\BookingExtension::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Scope: bookings that are currently "active" (blocking a room).
     * Includes confirmed/waiting bookings whose expires_at hasn't passed yet.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            // Confirmed or checked-in — always block
            $q->whereIn('status', ['confirmed', 'checked_in'])
              ->orWhere(function ($q2) {
                  // Waiting (pending) — only block if not yet expired
                  $q2->where('status', 'waiting')
                     ->where(function ($q3) {
                         $q3->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                     });
              });
        });
    }

    /**
     * Scope: rooms that have a conflicting booking between $checkin and $checkout.
     *
     * Overlap condition (Allen's interval algebra):
     *   existing.start_date < requested.checkout
     *   AND existing.end_date > requested.checkin
     *
     * This catches all overlap cases:
     *   - Existing booking fully inside requested range
     *   - Requested range fully inside existing booking
     *   - Partial overlaps on either side
     */
    public function scopeOverlapping($query, string $checkin, string $checkout)
    {
        return $query->where('start_date', '<', $checkout)
                     ->where('end_date', '>', $checkin);
    }

    /**
     * Bulletproof scope: find bookings that conflict with the given date range,
     * considering only active (non-expired, non-cancelled) bookings.
     *
     * Usage:
     *   $conflicting = Booking::availableBetween($checkin, $checkout)
     *                         ->where('room_id', $roomId)
     *                         ->exists();
     *
     *   // Or get all unavailable room IDs:
     *   $takenRoomIds = Booking::unavailableRoomIds($checkin, $checkout);
     */
    public function scopeAvailableBetween($query, string $checkin, string $checkout)
    {
        return $query->active()->overlapping($checkin, $checkout);
    }

    // -------------------------------------------------------------------------
    // Static helpers
    // -------------------------------------------------------------------------

    /**
     * Returns array of room IDs that are NOT available between the given dates.
     */
    public static function unavailableRoomIds(string $checkin, string $checkout): array
    {
        return self::availableBetween($checkin, $checkout)
                   ->pluck('room_id')
                   ->unique()
                   ->values()
                   ->toArray();
    }

    /**
     * Check if a specific room is available between the given dates.
     * Optionally exclude a booking ID (useful when editing an existing booking).
     */
    public static function isRoomAvailable(
        int $roomId,
        string $checkin,
        string $checkout,
        ?int $excludeBookingId = null
    ): bool {
        $query = self::availableBetween($checkin, $checkout)
                     ->where('room_id', $roomId);

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return !$query->exists();
    }

    /**
     * Get the total deposit amount for the entire booking group.
     */
    public function getTotalGroupDeposit(): float
    {
        $depositPercent = (float) config('booking.deposit_percentage', 50) / 100;
        
        if (empty($this->group_id)) {
            return (float) ($this->deposit_amount ?? ($this->total_amount * $depositPercent));
        }

        return (float) self::where('group_id', $this->group_id)
            ->get(['deposit_amount', 'total_amount'])
            ->sum(fn($b) => $b->deposit_amount ?? round(($b->total_amount ?? 0) * $depositPercent, 2));
    }

    /**
     * Set expires_at when creating a pending booking.
     * Call this after creating a waiting booking.
     */
    public function setExpiry(int $minutes = self::PENDING_TTL_MINUTES): void
    {
        $this->update(['expires_at' => now()->addMinutes($minutes)]);
    }

    /**
     * Check if this booking's hold has expired.
     */
    public function isExpired(): bool
    {
        return $this->status === 'waiting'
            && $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    // -------------------------------------------------------------------------
    // Legacy / utility scopes (kept for backward compatibility)
    // -------------------------------------------------------------------------

    public function scopeForDate($query, $date, string $column = 'created_at')
    {
        return $query->whereDate($column, $date);
    }

    public function scopeBetweenDates($query, $from, $to, string $column = 'created_at')
    {
        return $query->whereBetween($column, [$from, $to]);
    }

    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // -------------------------------------------------------------------------
    // Cart helper
    // -------------------------------------------------------------------------

    public static function insertFromCart($cart)
    {
        foreach ($cart as $item) {
            $data = [
                'group_id'              => $item['group_id'] ?? null,
                'room_id'               => $item['room_id'],
                'name'                  => $item['guest_info']['name'] ?? null,
                'email'                 => $item['guest_info']['email'] ?? null,
                'phone'                 => $item['guest_info']['phone'] ?? null,
                'start_date'            => $item['start_date'],
                'end_date'              => $item['end_date'],
                'scheduled_checkin_at'  => $item['scheduled_checkin_at'] ?? null,
                'scheduled_checkout_at' => $item['scheduled_checkout_at'] ?? null,
                'adults'                => $item['adults'],
                'children'              => $item['children'],
                'total_amount'          => $item['total_amount'] ?? 0,
                'nights'                => $item['nights'] ?? 1,
                'status'                => 'waiting',
                'expires_at'            => now()->addMinutes(self::PENDING_TTL_MINUTES),
            ];
            self::create($data);
        }
    }
}
