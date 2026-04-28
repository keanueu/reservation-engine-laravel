<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

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
        // Deposit-related fields
        'deposit_amount',
        'deposit_fee',
        'total_to_charge',
        // Refund-related fields (nullable)
        'refund_status',      // e.g. null | requested | processing | refunded | rejected
        'refund_requested_amount',
        'refund_fee',
        'refund_amount',
        'refund_reason',
        'refunded_at',
        'nights',
        // Check-in / checkout datetimes
        'scheduled_checkin_at',
        'scheduled_checkout_at',
        'actual_checkin_at',
        'actual_checkout_at',
    ];

    protected $casts = [
        'scheduled_checkin_at' => 'datetime',
        'scheduled_checkout_at' => 'datetime',
        'actual_checkin_at' => 'datetime',
        'actual_checkout_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function extensions()
    {
        return $this->hasMany(\App\Models\BookingExtension::class);
    }

    /**
     * Insert all bookings from a cart array (session 'booking_cart').
     */
    public static function insertFromCart($cart)
    {
        foreach ($cart as $item) {
            $data = [
                'group_id' => $item['group_id'] ?? null,
                'room_id' => $item['room_id'],
                'name' => $item['guest_info']['name'] ?? null,
                'email' => $item['guest_info']['email'] ?? null,
                'phone' => $item['guest_info']['phone'] ?? null,
                'start_date' => $item['start_date'],
                'end_date' => $item['end_date'],
                'scheduled_checkin_at' => $item['scheduled_checkin_at'] ?? null,
                'scheduled_checkout_at' => $item['scheduled_checkout_at'] ?? null,
                'adults' => $item['adults'],
                'children' => $item['children'],
                'total_amount' => $item['total_amount'] ?? 0,
                'nights' => $item['nights'] ?? 1,
            ];
            self::create($data);
        }
    }
}
