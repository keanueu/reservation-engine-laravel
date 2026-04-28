<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'admin_id',
        'sender',
        'message',
        'meta',
        'requires_admin',
    ];

    protected $casts = [
        'meta' => 'array',
        'requires_admin' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
