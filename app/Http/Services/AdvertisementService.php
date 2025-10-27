<?php

namespace App\Http\Services;

use App\Models\Advertisement\Advertisement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AdvertisementService
{
    /**
     * Get filtered advertisements with pagination.
     */
    public function getFilteredAdvertisements(Request $request)
    {
        $query = Advertisement::query()
            ->active()
            ->published()
            ->notExpired()
            ->with(['city', 'category', 'user', 'galleries']);

        // Apply filters
        $this->applyFilters($query, $request);

        // Apply sorting
        $this->applySorting($query, $request);

        // Paginate results
        return $query->paginate(20);
    }

    /**
     * Get single advertisement by slug with related data.
     */
    public function getAdvertisementBySlug(string $slug)
    {
        return Advertisement::where('slug', $slug)
            ->active()
            ->published()
            ->notExpired()
            ->with([
                'city',
                'category',
                'user',
                'galleries',
                'categoryValues.categoryAttribute'
            ])
            ->first();
    }

    /**
     * Get related advertisements.
     */
    public function getRelatedAdvertisements(Advertisement $advertisement, int $limit = 6)
    {
        return Advertisement::where('id', '!=', $advertisement->id)
            ->where('category_id', $advertisement->category_id)
            ->where('city_id', $advertisement->city_id)
            ->active()
            ->published()
            ->notExpired()
            ->with(['city', 'category'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get advertisements by category with attributes.
     */
    public function getAdvertisementsByCategory(int $categoryId, Request $request)
    {
        $query = Advertisement::where('category_id', $categoryId)
            ->active()
            ->published()
            ->notExpired()
            ->with(['city', 'category', 'user', 'galleries']);

        // Apply filters
        $this->applyFilters($query, $request);

        // Apply sorting
        $this->applySorting($query, $request);

        return $query->paginate(20);
    }

    /**
     * Get category attributes for filtering.
     */
    public function getCategoryAttributes(int $categoryId)
    {
        return \App\Models\Category\Category::with(['attributes.values' => function ($query) {
            $query->active();
        }])
        ->where('id', $categoryId)
        ->first();
    }

    /**
     * Apply filters to the query.
     */
    protected function applyFilters(Builder $query, Request $request): void
    {
        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            if (strlen($search) >= 2) {
                $query->search($search);
            }
        }

        // City filter
        if ($request->filled('city_id')) {
            $query->byCity($request->get('city_id'));
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->byCategory($request->get('category_id'));
        }

        // Price range filter
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->priceRange(
                $request->get('min_price'),
                $request->get('max_price')
            );
        }

        // Ads type filter
        if ($request->filled('ads_type')) {
            $query->byAdsType($request->get('ads_type'));
        }

        // Ads status filter
        if ($request->filled('ads_status')) {
            $query->byAdsStatus($request->get('ads_status'));
        }

        // Category values filter
        if ($request->filled('category_values')) {
            $categoryValues = $request->get('category_values');
            if (is_array($categoryValues)) {
                $query->byCategoryValues($categoryValues);
            }
        }

        // Special ads filter
        if ($request->filled('is_special')) {
            $query->where('is_special', $request->boolean('is_special'));
        }

        // Ladder ads filter
        if ($request->filled('is_ladder')) {
            $query->where('is_ladder', $request->boolean('is_ladder'));
        }

        // Willing to trade filter
        if ($request->filled('willing_to_trade')) {
            $query->where('willing_to_trade', $request->boolean('willing_to_trade'));
        }
    }

    /**
     * Apply sorting to the query.
     */
    protected function applySorting(Builder $query, Request $request): void
    {
        $sortBy = $request->get('sort_by', 'newest');

        // Always prioritize ladder advertisements first
        $query->orderBy('is_ladder', 'desc');

        switch ($sortBy) {
            case 'newest':
                $query->orderBy('published_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'views':
                $query->orderBy('view', 'desc');
                break;
            case 'most_relevant':
                // For now, just sort by newest
                // In future, implement relevance scoring
                $query->orderBy('published_at', 'desc');
                break;
            default:
                $query->orderBy('published_at', 'desc');
                break;
        }
    }

    /**
     * Increment advertisement view count.
     */
    public function incrementView(Advertisement $advertisement): void
    {
        $advertisement->incrementView();
    }

    /**
     * Get filter options for the frontend.
     */
    public function getFilterOptions()
    {
        return [
            'sort_options' => [
                ['value' => 'newest', 'label' => __('messages.filters.sort_by.newest')],
                ['value' => 'oldest', 'label' => __('messages.filters.sort_by.oldest')],
                ['value' => 'price_asc', 'label' => __('messages.filters.sort_by.price_asc')],
                ['value' => 'price_desc', 'label' => __('messages.filters.sort_by.price_desc')],
                ['value' => 'views', 'label' => __('messages.filters.sort_by.views')],
                ['value' => 'most_relevant', 'label' => __('messages.filters.sort_by.most_relevant')],
            ],
            'ads_types' => [
                ['value' => 'sell', 'label' => __('messages.filters.ads_type.sell')],
                ['value' => 'buy', 'label' => __('messages.filters.ads_type.buy')],
                ['value' => 'rent', 'label' => __('messages.filters.ads_type.rent')],
                ['value' => 'exchange', 'label' => __('messages.filters.ads_type.exchange')],
            ],
            'ads_statuses' => [
                ['value' => 'as_good_as_new', 'label' => __('messages.filters.ads_status.as_good_as_new')],
                ['value' => 'excellent', 'label' => __('messages.filters.ads_status.excellent')],
                ['value' => 'good', 'label' => __('messages.filters.ads_status.good')],
                ['value' => 'fair', 'label' => __('messages.filters.ads_status.fair')],
                ['value' => 'poor', 'label' => __('messages.filters.ads_status.poor')],
            ],
        ];
    }
}
