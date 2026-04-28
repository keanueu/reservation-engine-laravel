<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boat extends Model
{
   protected $fillable = [
        'name',
        'description',
        'price',
        'capacity', 
        'image',
        'quantity',
        'status',
        'start_time',
        'end_time'
    ];


public function bookings()
{
    return $this->hasMany(Booking::class, 'boat_id');
}


}

