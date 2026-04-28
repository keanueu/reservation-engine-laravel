<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $table = 'alerts'; // assume table exists (no migration created per request)

    protected $fillable = [
        'title', 'severity', 'message', 'location', 'starts_at', 'ends_at', 'send_email', 'meta'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'send_email' => 'boolean',
        'meta' => 'array'
    ];
}
