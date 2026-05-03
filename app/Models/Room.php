<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_name',
        'image',
        'description',
        'price',
        'accommodates',
        'terms',
        'room_type',
        'beds',
        'amenities',
        'check_in',
        'check_out'
    ];

    public function images()
    {
        return $this->hasMany(\App\Models\Images::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_room');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function prices()
    {
        return $this->hasMany(RoomPrice::class)->where('is_active', true)->orderByDesc('priority');
    }

    /**
     * Shortcut: calculate dynamic pricing for a date range.
     */
    public function calculatePrice(string $checkin, string $checkout): array
    {
        return RoomPrice::calculateForRoom($this, $checkin, $checkout);
    }

    /**
     * Check if this room is available between the given dates.
     */
    public function isAvailableBetween(string $checkin, string $checkout, ?int $excludeBookingId = null): bool
    {
        return Booking::isRoomAvailable($this->id, $checkin, $checkout, $excludeBookingId);
    }

    /**
     * Scope: only rooms that are available between the given dates.
     *
     * Usage: Room::availableBetween('2025-12-01', '2025-12-05')->get();
     */
    public function scopeAvailableBetween($query, string $checkin, string $checkout)
    {
        $takenIds = Booking::unavailableRoomIds($checkin, $checkout);

        return $query->whereNotIn('id', $takenIds);
    }
}
