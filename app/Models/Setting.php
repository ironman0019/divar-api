<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'logo',
        'favicon',
        'email',
        'phone',
        'keywords'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the first setting record (singleton pattern for settings)
     */
    public static function getFirst()
    {
        return static::first() ?? new static();
    }

    /**
     * Update or create the first setting record
     */
    public static function updateOrCreateFirst(array $data)
    {
        return static::firstOrCreate(['id' => 1], $data);
    }

    /**
     * Get setting value by key
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::first();
        return $setting ? $setting->$key ?? $default : $default;
    }

    /**
     * Set setting value by key
     */
    public static function setValue($key, $value)
    {
        $setting = static::firstOrCreate(['id' => 1]);
        $setting->update([$key => $value]);
        return $setting;
    }
}
