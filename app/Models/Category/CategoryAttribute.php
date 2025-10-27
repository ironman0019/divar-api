<?php

namespace App\Models\Category;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryAttribute extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'unit',
        'type',
        'category_id',
        'status',
    ];

    protected $casts = [
        'type' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Get the category that owns the attribute.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the values for the attribute.
     */
    public function values(): HasMany
    {
        return $this->hasMany(CategoryValue::class);
    }

    /**
     * Scope a query to only include active attributes.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Get attribute type labels.
     */
    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            0 => 'متن',
            1 => 'عدد',
            2 => 'انتخاب',
            3 => 'چند انتخابی',
            default => 'نامشخص',
        };
    }
}
