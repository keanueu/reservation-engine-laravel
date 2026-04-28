<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['key', 'value'];
    public $timestamps = true;

    /**
     * Get a setting value with optional default and cast if numeric/boolean.
     */
    public static function get($key, $default = null)
    {
        $row = static::where('key', $key)->first();
        if (!$row) return $default;
        $val = $row->value;
        // attempt to cast booleans and numerics
        if ($val === '0' || $val === '1') {
            return $val === '1' ? 1 : 0;
        }
        if (is_numeric($val)) {
            if (strpos($val, '.') !== false) return (float) $val;
            return (int) $val;
        }
        return $val;
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }
}
