<?php

namespace App\Models\Advertisement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromotionPrice extends Model
{
    use SoftDeletes;

    // Promotion type constants
    const TYPE_LADDER = 'ladder';
    const TYPE_SPECIAL = 'special';

    protected $fillable = [
        'type',
        'duration_days',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active prices.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by promotion type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by duration.
     */
    public function scopeByDuration($query, int $durationDays)
    {
        return $query->where('duration_days', $durationDays);
    }

    /**
     * Get promotion type label in Persian.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_LADDER => 'نردبان',
            self::TYPE_SPECIAL => 'ویژه',
            default => 'نامشخص',
        };
    }

    /**
     * Get formatted price with currency.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price) . ' تومان';
    }

    /**
     * Get duration label.
     */
    public function getDurationLabelAttribute(): string
    {
        return $this->duration_days . ' روز';
    }
}
