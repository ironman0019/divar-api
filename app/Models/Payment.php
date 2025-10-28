<?php

namespace App\Models;

use App\Models\Advertisement\Advertisement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    // Payment status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';

    // Payment type constants
    const TYPE_LADDER = 'ladder';
    const TYPE_SPECIAL = 'special';

    protected $fillable = [
        'user_id',
        'advertisement_id',
        'amount',
        'payment_type',
        'duration_days',
        'description',
        'status',
        'authority',
        'ref_id',
        'card_pan',
        'trace_no',
        'gateway_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'duration_days' => 'integer',
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the advertisement that the payment is for.
     */
    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if payment is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Mark payment as paid.
     */
    public function markAsPaid(?string $refId = null): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'ref_id' => $refId,
        ]);
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(?string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'description' => $reason ? $this->description . ' - ' . $reason : $this->description,
        ]);
    }

    /**
     * Get payment type label in Persian.
     */
    public function getPaymentTypeLabelAttribute(): string
    {
        return match ($this->payment_type) {
            self::TYPE_LADDER => 'نردبان',
            self::TYPE_SPECIAL => 'ویژه',
            default => 'نامشخص',
        };
    }

    /**
     * Get status label in Persian.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'در انتظار',
            self::STATUS_PAID => 'پرداخت شده',
            self::STATUS_FAILED => 'ناموفق',
            default => 'نامشخص',
        };
    }
}
