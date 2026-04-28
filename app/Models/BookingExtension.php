<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingExtension extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'hours',
        'requested_by',
        'status', // pending_payment, pending_frontdesk, approved, paid, declined
        'price',
        'payment_id',
        'processed_by',
        'processed_at',
        'new_checkout_at',
        'notes',
    ];

    protected $casts = [
        'new_checkout_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
