<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'name', 'description', 'type', 'amount', 'amount_type',
        'active', 'start_date', 'end_date'
    ];

    public function images()
    {
        return $this->hasMany(DiscountImage::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'discount_room');
    }

    public function isActive()
    {
        // Treat null start_date or end_date as open-ended
        if (! $this->active) {
            return false;
        }

        $now = \Carbon\Carbon::now();

        if ($this->start_date) {
            try {
                $start = \Carbon\Carbon::parse($this->start_date);
            } catch (\Exception $e) {
                $start = null;
            }
            if ($start && $now->lt($start)) {
                return false;
            }
        }

        if ($this->end_date) {
            try {
                $end = \Carbon\Carbon::parse($this->end_date);
            } catch (\Exception $e) {
                $end = null;
            }
            if ($end && $now->gt($end)) {
                return false;
            }
        }

        return true;
    }
}
