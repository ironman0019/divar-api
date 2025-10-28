<?php

namespace Database\Seeders;

use App\Models\Advertisement\PromotionPrice;
use Illuminate\Database\Seeder;

class PromotionPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promotionPrices = [
            // Ladder promotions (نردبان)
            [
                'type' => PromotionPrice::TYPE_LADDER,
                'duration_days' => 7,
                'price' => 50000.00,
                'is_active' => true,
            ],
            [
                'type' => PromotionPrice::TYPE_LADDER,
                'duration_days' => 15,
                'price' => 90000.00,
                'is_active' => true,
            ],
            [
                'type' => PromotionPrice::TYPE_LADDER,
                'duration_days' => 30,
                'price' => 150000.00,
                'is_active' => true,
            ],
            
            // Special promotions (ویژه)
            [
                'type' => PromotionPrice::TYPE_SPECIAL,
                'duration_days' => 7,
                'price' => 100000.00,
                'is_active' => true,
            ],
            [
                'type' => PromotionPrice::TYPE_SPECIAL,
                'duration_days' => 15,
                'price' => 180000.00,
                'is_active' => true,
            ],
            [
                'type' => PromotionPrice::TYPE_SPECIAL,
                'duration_days' => 30,
                'price' => 300000.00,
                'is_active' => true,
            ],
        ];

        foreach ($promotionPrices as $priceData) {
            PromotionPrice::updateOrCreate(
                [
                    'type' => $priceData['type'],
                    'duration_days' => $priceData['duration_days'],
                ],
                $priceData
            );
        }
    }
}
