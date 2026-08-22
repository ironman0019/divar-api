<?php

namespace App\Http\Services;


use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Advertisement\Advertisement;
use App\Models\Advertisement\PromotionPrice;
use App\Support\CatalogCache;
use App\Models\Advertisement\FeaturedAdvertisement;

class AdvertisementPromotionService
{
    /**
     * Get available promotion options for an advertisement.
     */
    public function getAvailablePromotions(int $advertisementId): array
    {
        $advertisement = Advertisement::find($advertisementId);
        
        if (!$advertisement) {
            throw new \Exception('Advertisement not found');
        }

        // Check if advertisement is active
        if ($advertisement->status !== 2) {
            throw new \Exception('Advertisement must be active to promote');
        }

        // Get all active promotion prices
        $promotionPrices = CatalogCache::activePromotionPrices();

        // Group by type
        $groupedPrices = $promotionPrices->groupBy('type');

        $result = [
            'advertisement' => [
                'id' => $advertisement->id,
                'title' => $advertisement->title,
                'is_ladder' => $advertisement->is_ladder,
                'is_special' => $advertisement->is_special,
            ],
            'promotions' => [
                'ladder' => [],
                'special' => [],
            ],
        ];

        foreach ($groupedPrices as $type => $prices) {
            $result['promotions'][$type] = $prices->map(function ($price) {
                return [
                    'id' => $price->id,
                    'duration_days' => $price->duration_days,
                    'duration_label' => $price->duration_label,
                    'price' => $price->price,
                    'formatted_price' => $price->formatted_price,
                    'type_label' => $price->type_label,
                ];
            })->toArray();
        }

        return $result;
    }

    /**
     * Apply promotion after successful payment.
     */
    public function applyPromotion(Payment $payment): void
    {
        try {
            DB::beginTransaction();

            $advertisement = $payment->advertisement;

            // Apply promotion based on type
            if ($payment->payment_type === Payment::TYPE_LADDER) {
                $this->applyLadderPromotion($advertisement, $payment);
            } elseif ($payment->payment_type === Payment::TYPE_SPECIAL) {
                $this->applySpecialPromotion($advertisement, $payment);
            }

            DB::commit();

            Log::info('Advertisement promotion applied successfully', [
                'payment_id' => $payment->id,
                'advertisement_id' => $advertisement->id,
                'promotion_type' => $payment->payment_type,
                'duration_days' => $payment->duration_days,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to apply advertisement promotion', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Apply ladder promotion to advertisement.
     */
    private function applyLadderPromotion(Advertisement $advertisement, Payment $payment): void
    {
        $advertisement->update(['is_ladder' => true]);

        // Create featured advertisement record for tracking
        FeaturedAdvertisement::create([
            'advertisement_id' => $advertisement->id,
            'payment_id' => $payment->id,
            'type' => Payment::TYPE_LADDER,
            'expires_at' => now()->addDays($payment->duration_days),
            'is_active' => true,
        ]);
    }

    /**
     * Apply special promotion to advertisement.
     */
    private function applySpecialPromotion(Advertisement $advertisement, Payment $payment): void
    {
        $advertisement->update(['is_special' => true]);

        // Create featured advertisement record
        FeaturedAdvertisement::create([
            'advertisement_id' => $advertisement->id,
            'payment_id' => $payment->id,
            'type' => Payment::TYPE_SPECIAL,
            'expires_at' => now()->addDays($payment->duration_days),
            'is_active' => true,
        ]);
    }

    /**
     * Get promotion price by type and duration.
     */
    public function getPromotionPrice(string $type, int $durationDays): ?PromotionPrice
    {
        return PromotionPrice::active()
            ->byType($type)
            ->byDuration($durationDays)
            ->first();
    }

    /**
     * Check if advertisement can be promoted.
     */
    public function canPromoteAdvertisement(int $advertisementId): bool
    {
        $advertisement = Advertisement::find($advertisementId);
        
        if (!$advertisement) {
            return false;
        }

        // Advertisement must be active (status = 2)
        return $advertisement->status === 2;
    }

    /**
     * Get active featured advertisements.
     */
    public function getActiveFeaturedAdvertisements(): array
    {
        return FeaturedAdvertisement::active()
            ->notExpired()
            ->with(['advertisement', 'payment'])
            ->get()
            ->toArray();
    }

    /**
     * Get expired featured advertisements.
     */
    public function getExpiredFeaturedAdvertisements(): array
    {
        return FeaturedAdvertisement::where('expires_at', '<=', now())
            ->with(['advertisement', 'payment'])
            ->get()
            ->toArray();
    }

    /**
     * Deactivate expired promotions.
     */
    public function deactivateExpiredPromotions(): int
    {
        $expiredCount = FeaturedAdvertisement::where('expires_at', '<=', now())
            ->where('is_active', true)
            ->count();

        FeaturedAdvertisement::where('expires_at', '<=', now())
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Also update advertisement flags if no active promotions
        $this->updateAdvertisementFlags();

        return $expiredCount;
    }

    /**
     * Update advertisement flags based on active promotions.
     */
    private function updateAdvertisementFlags(): void
    {
        // Get advertisements that have no active ladder promotions
        Advertisement::where('is_ladder', true)
            ->whereDoesntHave('featuredAdvertisements', function ($query) {
                $query->where('type', Payment::TYPE_LADDER)
                    ->where('is_active', true)
                    ->where('expires_at', '>', now());
            })
            ->update(['is_ladder' => false]);

        // Get advertisements that have no active special promotions
        Advertisement::where('is_special', true)
            ->whereDoesntHave('featuredAdvertisements', function ($query) {
                $query->where('type', Payment::TYPE_SPECIAL)
                    ->where('is_active', true)
                    ->where('expires_at', '>', now());
            })
            ->update(['is_special' => false]);
    }
}
