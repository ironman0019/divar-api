<?php

namespace App\Models\Advertisement;

use App\Models\Advertisement\Advertisement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeaturedAdvertisement extends Model
{
    use SoftDeletes;

    // Promotion type constants
    const TYPE_LADDER = 'ladder';
    const TYPE_SPECIAL = 'special';

    protected $fillable = [
        'advertisement_id',
        'payment_id',
        'type',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the advertisement that owns the featured advertisement.
     */
    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    /**
     * Get the payment that owns the featured advertisement.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Payment::class);
    }

    /**
     * Scope a query to only include active featured advertisements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include non-expired featured advertisements.
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope a query to filter by promotion type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if the featured advertisement is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at <= now();
    }

    /**
     * Check if the featured advertisement is active and not expired.
     */
    public function isActive(): bool
    {
        return $this->is_active && !$this->isExpired();
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
     * Get remaining days until expiration.
     */
    public function getRemainingDaysAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->expires_at, false);
    }
}
