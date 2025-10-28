<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category\CategoryAttribute;
use App\Models\Category\Category;

class CategoryAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $apartmentCategory = Category::where('slug', 'apartment')->first();
        $carCategory = Category::where('slug', 'car')->first();
        $mobileCategory = Category::where('slug', 'mobile-tablet')->first();
        $laptopCategory = Category::where('slug', 'laptop-computer')->first();
        $kitchenCategory = Category::where('slug', 'kitchen-appliances')->first();

        $attributes = [
            // Apartment attributes
            [
                'name' => 'متراژ',
                'unit' => 'متر مربع',
                'type' => 1, // Number
                'category_id' => $apartmentCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'تعداد اتاق',
                'unit' => 'اتاق',
                'type' => 1, // Number
                'category_id' => $apartmentCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'طبقه',
                'unit' => 'طبقه',
                'type' => 1, // Number
                'category_id' => $apartmentCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'سن بنا',
                'unit' => 'سال',
                'type' => 1, // Number
                'category_id' => $apartmentCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'نوع آپارتمان',
                'unit' => '',
                'type' => 2, // Select
                'category_id' => $apartmentCategory->id,
                'status' => 1,
            ],

            // Car attributes
            [
                'name' => 'سال تولید',
                'unit' => 'سال',
                'type' => 1, // Number
                'category_id' => $carCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'کیلومتر',
                'unit' => 'کیلومتر',
                'type' => 1, // Number
                'category_id' => $carCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'نوع سوخت',
                'unit' => '',
                'type' => 2, // Select
                'category_id' => $carCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'رنگ',
                'unit' => '',
                'type' => 2, // Select
                'category_id' => $carCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'نوع گیربکس',
                'unit' => '',
                'type' => 2, // Select
                'category_id' => $carCategory->id,
                'status' => 1,
            ],

            // Mobile attributes
            [
                'name' => 'برند',
                'unit' => '',
                'type' => 2, // Select
                'category_id' => $mobileCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'مدل',
                'unit' => '',
                'type' => 0, // Text
                'category_id' => $mobileCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'رنگ',
                'unit' => '',
                'type' => 2, // Select
                'category_id' => $mobileCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'حافظه داخلی',
                'unit' => 'گیگابایت',
                'type' => 1, // Number
                'category_id' => $mobileCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'حافظه رم',
                'unit' => 'گیگابایت',
                'type' => 1, // Number
                'category_id' => $mobileCategory->id,
                'status' => 1,
            ],

            // Laptop attributes
            [
                'name' => 'برند',
                'unit' => '',
                'type' => 2, // Select
                'category_id' => $laptopCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'مدل',
                'unit' => '',
                'type' => 0, // Text
                'category_id' => $laptopCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'پردازنده',
                'unit' => '',
                'type' => 0, // Text
                'category_id' => $laptopCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'حافظه رم',
                'unit' => 'گیگابایت',
                'type' => 1, // Number
                'category_id' => $laptopCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'حافظه داخلی',
                'unit' => 'گیگابایت',
                'type' => 1, // Number
                'category_id' => $laptopCategory->id,
                'status' => 1,
            ],

            // Kitchen appliances attributes
            [
                'name' => 'برند',
                'unit' => '',
                'type' => 2, // Select
                'category_id' => $kitchenCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'مدل',
                'unit' => '',
                'type' => 0, // Text
                'category_id' => $kitchenCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'سن دستگاه',
                'unit' => 'سال',
                'type' => 1, // Number
                'category_id' => $kitchenCategory->id,
                'status' => 1,
            ],
            [
                'name' => 'وضعیت',
                'unit' => '',
                'type' => 2, // Select
                'category_id' => $kitchenCategory->id,
                'status' => 1,
            ],
        ];

        foreach ($attributes as $attribute) {
            CategoryAttribute::create($attribute);
        }
    }
}
