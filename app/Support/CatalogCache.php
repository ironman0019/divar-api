<?php

namespace App\Support;

use App\Models\Advertisement\PromotionPrice;
use App\Models\City;
use Illuminate\Support\Facades\Cache;

class CatalogCache
{
    public const TTL = 86400; // 24 hours

    public static function citiesKey(): string
    {
        return 'cities:all';
    }

    public static function categoriesIndexKey(string $variant = 'all'): string
    {
        return "categories:index:{$variant}";
    }

    public static function categoriesHierarchyKey(): string
    {
        return 'categories:hierarchy';
    }

    public static function categoriesShowKey(int $id): string
    {
        return "categories:show:{$id}";
    }

    public static function categoriesAttributesKey(int $id): string
    {
        return "categories:attributes:{$id}";
    }

    public static function categoriesChildrenKey(int $parentId): string
    {
        return "categories:children:{$parentId}";
    }

    public static function promotionPricesKey(): string
    {
        return 'promotion-prices:active';
    }

    public static function rememberCities(callable $callback)
    {
        return Cache::remember(self::citiesKey(), self::TTL, $callback);
    }

    public static function rememberCategories(string $key, callable $callback)
    {
        return Cache::tags(['categories'])->remember($key, self::TTL, $callback);
    }

    public static function rememberPromotionPrices(callable $callback)
    {
        return Cache::remember(self::promotionPricesKey(), self::TTL, $callback);
    }

    public static function forgetCities(): void
    {
        Cache::forget(self::citiesKey());
    }

    public static function forgetCategories(): void
    {
        Cache::tags(['categories'])->flush();
    }

    public static function forgetPromotionPrices(): void
    {
        Cache::forget(self::promotionPricesKey());
    }

    public static function activeCities()
    {
        return self::rememberCities(fn () => City::where('status', 1)->orderBy('name')->get());
    }

    public static function activePromotionPrices()
    {
        return self::rememberPromotionPrices(
            fn () => PromotionPrice::active()
                ->orderBy('type')
                ->orderBy('duration_days')
                ->get()
        );
    }
}
