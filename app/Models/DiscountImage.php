<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountImage extends Model
{
    protected $fillable = ['discount_id', 'filename'];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
