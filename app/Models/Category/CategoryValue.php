<?php

namespace App\Models\Category;

use App\Models\Advertisement\Advertisement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryValue extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_attribute_id',
        'value',
        'type',
        'status',
    ];

    protected $casts = [
        'type' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Get the category attribute that owns the value.
     */
    public function categoryAttribute(): BelongsTo
    {
        return $this->belongsTo(CategoryAttribute::class);
    }

    /**
     * Get the advertisements that have this category value.
     */
    public function advertisements(): BelongsToMany
    {
        return $this->belongsToMany(Advertisement::class, 'advertisement_category_values');
    }

    /**
     * Scope a query to only include active values.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Get value type labels.
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
