<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoatBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'boat_id',
        'name',
        'email',
        'phone',
        'booking_date',
        'start_time',
        'end_time',
        'guests',
        'total_amount',
        'paid_amount',
        'paid_at',
        // Deposit-related fields
        'deposit_amount',
        'deposit_fee',
        'total_to_charge',
        'status',
        'payment_id',
        'payment_status',
    ];

    public function boat()
    {
        return $this->belongsTo(Boat::class);
    }

    
}
