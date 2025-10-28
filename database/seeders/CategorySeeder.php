<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Main categories
        $mainCategories = [
            [
                'name' => 'املاک',
                'slug' => 'real-estate',
                'description' => 'خرید و فروش و اجاره املاک',
                'icon' => 'home',
                'parent_id' => null,
                'status' => 1,
            ],
            [
                'name' => 'خودرو',
                'slug' => 'vehicles',
                'description' => 'خرید و فروش خودرو و موتورسیکلت',
                'icon' => 'car',
                'parent_id' => null,
                'status' => 1,
            ],
            [
                'name' => 'استخدام',
                'slug' => 'jobs',
                'description' => 'آگهی‌های استخدام و کاریابی',
                'icon' => 'briefcase',
                'parent_id' => null,
                'status' => 1,
            ],
            [
                'name' => 'کالای دیجیتال',
                'slug' => 'digital-goods',
                'description' => 'موبایل، لپ‌تاپ، تبلت و لوازم جانبی',
                'icon' => 'smartphone',
                'parent_id' => null,
                'status' => 1,
            ],
            [
                'name' => 'خانه و آشپزخانه',
                'slug' => 'home-kitchen',
                'description' => 'لوازم خانگی و آشپزخانه',
                'icon' => 'home',
                'parent_id' => null,
                'status' => 1,
            ],
            [
                'name' => 'ورزش و سرگرمی',
                'slug' => 'sports-entertainment',
                'description' => 'لوازم ورزشی و سرگرمی',
                'icon' => 'gamepad',
                'parent_id' => null,
                'status' => 1,
            ],
        ];

        $createdCategories = [];
        foreach ($mainCategories as $category) {
            $createdCategories[] = Category::create($category);
        }

        // Sub-categories for املاک
        $realEstateSubCategories = [
            ['name' => 'آپارتمان', 'slug' => 'apartment', 'parent_id' => $createdCategories[0]->id],
            ['name' => 'خانه ویلایی', 'slug' => 'villa', 'parent_id' => $createdCategories[0]->id],
            ['name' => 'زمین', 'slug' => 'land', 'parent_id' => $createdCategories[0]->id],
            ['name' => 'مغازه', 'slug' => 'shop', 'parent_id' => $createdCategories[0]->id],
            ['name' => 'دفتر کار', 'slug' => 'office', 'parent_id' => $createdCategories[0]->id],
        ];

        foreach ($realEstateSubCategories as $subCategory) {
            Category::create(array_merge($subCategory, [
                'description' => 'املاک ' . $subCategory['name'],
                'icon' => 'home',
                'status' => 1,
            ]));
        }

        // Sub-categories for خودرو
        $vehicleSubCategories = [
            ['name' => 'خودرو سواری', 'slug' => 'car', 'parent_id' => $createdCategories[1]->id],
            ['name' => 'موتورسیکلت', 'slug' => 'motorcycle', 'parent_id' => $createdCategories[1]->id],
            ['name' => 'کامیون و مینی‌بوس', 'slug' => 'truck', 'parent_id' => $createdCategories[1]->id],
            ['name' => 'قطعات خودرو', 'slug' => 'car-parts', 'parent_id' => $createdCategories[1]->id],
        ];

        foreach ($vehicleSubCategories as $subCategory) {
            Category::create(array_merge($subCategory, [
                'description' => 'خودرو ' . $subCategory['name'],
                'icon' => 'car',
                'status' => 1,
            ]));
        }

        // Sub-categories for کالای دیجیتال
        $digitalSubCategories = [
            ['name' => 'موبایل و تبلت', 'slug' => 'mobile-tablet', 'parent_id' => $createdCategories[3]->id],
            ['name' => 'لپ‌تاپ و کامپیوتر', 'slug' => 'laptop-computer', 'parent_id' => $createdCategories[3]->id],
            ['name' => 'لوازم جانبی', 'slug' => 'accessories', 'parent_id' => $createdCategories[3]->id],
            ['name' => 'گیم و کنسول', 'slug' => 'gaming', 'parent_id' => $createdCategories[3]->id],
        ];

        foreach ($digitalSubCategories as $subCategory) {
            Category::create(array_merge($subCategory, [
                'description' => 'کالای دیجیتال ' . $subCategory['name'],
                'icon' => 'smartphone',
                'status' => 1,
            ]));
        }

        // Sub-categories for خانه و آشپزخانه
        $homeSubCategories = [
            ['name' => 'لوازم آشپزخانه', 'slug' => 'kitchen-appliances', 'parent_id' => $createdCategories[4]->id],
            ['name' => 'مبلمان و دکوراسیون', 'slug' => 'furniture', 'parent_id' => $createdCategories[4]->id],
            ['name' => 'لوازم برقی خانگی', 'slug' => 'home-appliances', 'parent_id' => $createdCategories[4]->id],
            ['name' => 'سرویس خواب', 'slug' => 'bedroom-set', 'parent_id' => $createdCategories[4]->id],
        ];

        foreach ($homeSubCategories as $subCategory) {
            Category::create(array_merge($subCategory, [
                'description' => 'خانه و آشپزخانه ' . $subCategory['name'],
                'icon' => 'home',
                'status' => 1,
            ]));
        }
    }
}
