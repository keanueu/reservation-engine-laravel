<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model for the single global status record.
 */
class CalamityStatus extends Model
{
    use HasFactory;

    // Use a guarded property to allow mass assignment for the 'status' field.
    protected $guarded = [];

    // Explicitly define the table name (optional, but good practice)
    protected $table = 'calamity_statuses';
}