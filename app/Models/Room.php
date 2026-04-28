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

    // Relationship to room images
    public function images()
    {
        return $this->hasMany(\App\Models\Images::class);
    }


    public function discounts()
{
    return $this->belongsToMany(Discount::class, 'discount_room');
}


}
