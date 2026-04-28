<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RegistrationOtp extends Model
{
    protected $fillable = ['user_id', 'otp_code', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function isExpired()
    {
        return $this->expires_at->lt(now());
    }
}
