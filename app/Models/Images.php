<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
     protected $fillable = [
        'image',
        'room_id',
    ];
}
