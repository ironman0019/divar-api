<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category\CategoryValue;
use App\Models\Category\CategoryAttribute;

class CategoryValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get attributes by category and name
        $apartmentTypeAttr = CategoryAttribute::whereHas('category', function($q) {
            $q->where('slug', 'apartment');
        })->where('name', 'نوع آپارتمان')->first();
        
        $fuelTypeAttr = CategoryAttribute::whereHas('category', function($q) {
            $q->where('slug', 'car');
        })->where('name', 'نوع سوخت')->first();
        
        $carColorAttr = CategoryAttribute::whereHas('category', function($q) {
            $q->where('slug', 'car');
        })->where('name', 'رنگ')->first();
        
        $gearboxAttr = CategoryAttribute::whereHas('category', function($q) {
            $q->where('slug', 'car');
        })->where('name', 'نوع گیربکس')->first();
        
        $mobileBrandAttr = CategoryAttribute::whereHas('category', function($q) {
            $q->where('slug', 'mobile-tablet');
        })->where('name', 'برند')->first();
        
        $mobileColorAttr = CategoryAttribute::whereHas('category', function($q) {
            $q->where('slug', 'mobile-tablet');
        })->where('name', 'رنگ')->first();
        
        $laptopBrandAttr = CategoryAttribute::whereHas('category', function($q) {
            $q->where('slug', 'laptop-computer');
        })->where('name', 'برند')->first();
        
        $kitchenBrandAttr = CategoryAttribute::whereHas('category', function($q) {
            $q->where('slug', 'kitchen-appliances');
        })->where('name', 'برند')->first();
        
        $kitchenConditionAttr = CategoryAttribute::whereHas('category', function($q) {
            $q->where('slug', 'kitchen-appliances');
        })->where('name', 'وضعیت')->first();

        $values = [
            // Apartment type values
            [
                'category_attribute_id' => $apartmentTypeAttr->id,
                'value' => 'نو',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $apartmentTypeAttr->id,
                'value' => 'نیمه‌نو',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $apartmentTypeAttr->id,
                'value' => 'قدیمی',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $apartmentTypeAttr->id,
                'value' => 'در حال ساخت',
                'type' => 0,
                'status' => 1,
            ],

            // Fuel type values
            [
                'category_attribute_id' => $fuelTypeAttr->id,
                'value' => 'بنزین',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $fuelTypeAttr->id,
                'value' => 'گاز',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $fuelTypeAttr->id,
                'value' => 'دوگانه‌سوز',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $fuelTypeAttr->id,
                'value' => 'هیبریدی',
                'type' => 0,
                'status' => 1,
            ],

            // Car color values
            [
                'category_attribute_id' => $carColorAttr->id,
                'value' => 'سفید',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $carColorAttr->id,
                'value' => 'سیاه',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $carColorAttr->id,
                'value' => 'نقره‌ای',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $carColorAttr->id,
                'value' => 'قرمز',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $carColorAttr->id,
                'value' => 'آبی',
                'type' => 0,
                'status' => 1,
            ],

            // Gearbox type values
            [
                'category_attribute_id' => $gearboxAttr->id,
                'value' => 'دستی',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $gearboxAttr->id,
                'value' => 'اتوماتیک',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $gearboxAttr->id,
                'value' => 'نیمه‌اتوماتیک',
                'type' => 0,
                'status' => 1,
            ],

            // Mobile brand values
            [
                'category_attribute_id' => $mobileBrandAttr->id,
                'value' => 'سامسونگ',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $mobileBrandAttr->id,
                'value' => 'اپل',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $mobileBrandAttr->id,
                'value' => 'هواوی',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $mobileBrandAttr->id,
                'value' => 'شیائومی',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $mobileBrandAttr->id,
                'value' => 'وان‌پلاس',
                'type' => 0,
                'status' => 1,
            ],

            // Mobile color values
            [
                'category_attribute_id' => $mobileColorAttr->id,
                'value' => 'سفید',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $mobileColorAttr->id,
                'value' => 'سیاه',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $mobileColorAttr->id,
                'value' => 'طلایی',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $mobileColorAttr->id,
                'value' => 'نقره‌ای',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $mobileColorAttr->id,
                'value' => 'آبی',
                'type' => 0,
                'status' => 1,
            ],

            // Laptop brand values
            [
                'category_attribute_id' => $laptopBrandAttr->id,
                'value' => 'ایسوس',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $laptopBrandAttr->id,
                'value' => 'لنوو',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $laptopBrandAttr->id,
                'value' => 'اچ‌پی',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $laptopBrandAttr->id,
                'value' => 'دل',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $laptopBrandAttr->id,
                'value' => 'اپل',
                'type' => 0,
                'status' => 1,
            ],

            // Kitchen brand values
            [
                'category_attribute_id' => $kitchenBrandAttr->id,
                'value' => 'سامسونگ',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $kitchenBrandAttr->id,
                'value' => 'ال‌جی',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $kitchenBrandAttr->id,
                'value' => 'بوش',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $kitchenBrandAttr->id,
                'value' => 'سیمنس',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $kitchenBrandAttr->id,
                'value' => 'ایندزیت',
                'type' => 0,
                'status' => 1,
            ],

            // Kitchen condition values
            [
                'category_attribute_id' => $kitchenConditionAttr->id,
                'value' => 'در حد نو',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $kitchenConditionAttr->id,
                'value' => 'عالی',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $kitchenConditionAttr->id,
                'value' => 'خوب',
                'type' => 0,
                'status' => 1,
            ],
            [
                'category_attribute_id' => $kitchenConditionAttr->id,
                'value' => 'متوسط',
                'type' => 0,
                'status' => 1,
            ],
        ];

        foreach ($values as $value) {
            CategoryValue::create($value);
        }
    }
}
