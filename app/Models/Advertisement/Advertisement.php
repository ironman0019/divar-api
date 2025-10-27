<?php

namespace App\Models\Advertisement;

use App\Models\Category\Category;
use App\Models\Category\CategoryValue;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advertisement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'ads_type',
        'ads_status',
        'category_id',
        'city_id',
        'user_id',
        'status',
        'published_at',
        'expired_at',
        'view',
        'contact',
        'is_special',
        'is_ladder',
        'image',
        'slug',
        'price',
        'tags',
        'lat',
        'lng',
        'willing_to_trade',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expired_at' => 'datetime',
        'price' => 'integer',
        'view' => 'integer',
        'is_special' => 'boolean',
        'is_ladder' => 'boolean',
        'willing_to_trade' => 'boolean',
        'status' => 'integer',
    ];

    /**
     * Get the user that owns the advertisement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the city that the advertisement belongs to.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the category that the advertisement belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the galleries for the advertisement.
     */
    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    /**
     * Get the category values for the advertisement.
     */
    public function categoryValues(): BelongsToMany
    {
        return $this->belongsToMany(CategoryValue::class, 'advertisement_category_values');
    }

    /**
     * Scope a query to only include active advertisements.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 2);
    }

    /**
     * Scope a query to only include published advertisements.
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include non-expired advertisements.
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expired_at')
              ->orWhere('expired_at', '>', now());
        });
    }

    /**
     * Scope a query to search advertisements by title and description.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopePriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }
        return $query;
    }

    /**
     * Scope a query to filter by city.
     */
    public function scopeByCity($query, $cityId)
    {
        if ($cityId) {
            return $query->where('city_id', $cityId);
        }
        return $query;
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        if ($categoryId) {
            return $query->where('category_id', $categoryId);
        }
        return $query;
    }

    /**
     * Scope a query to filter by ads type.
     */
    public function scopeByAdsType($query, $adsType)
    {
        if ($adsType) {
            return $query->where('ads_type', $adsType);
        }
        return $query;
    }

    /**
     * Scope a query to filter by ads status.
     */
    public function scopeByAdsStatus($query, $adsStatus)
    {
        if ($adsStatus) {
            return $query->where('ads_status', $adsStatus);
        }
        return $query;
    }

    /**
     * Scope a query to filter by category values.
     */
    public function scopeByCategoryValues($query, $categoryValueIds)
    {
        if ($categoryValueIds && is_array($categoryValueIds)) {
            return $query->whereHas('categoryValues', function ($q) use ($categoryValueIds) {
                $q->whereIn('category_values.id', $categoryValueIds);
            });
        }
        return $query;
    }

    /**
     * Increment view count.
     */
    public function incrementView()
    {
        $this->increment('view');
    }
}
