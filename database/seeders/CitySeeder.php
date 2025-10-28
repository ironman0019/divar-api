<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['name' => 'تهران', 'status' => 1],
            ['name' => 'مشهد', 'status' => 1],
            ['name' => 'اصفهان', 'status' => 1],
            ['name' => 'شیراز', 'status' => 1],
            ['name' => 'تبریز', 'status' => 1],
            ['name' => 'کرج', 'status' => 1],
            ['name' => 'اهواز', 'status' => 1],
            ['name' => 'قم', 'status' => 1],
            ['name' => 'کرمانشاه', 'status' => 1],
            ['name' => 'ارومیه', 'status' => 1],
            ['name' => 'زاهدان', 'status' => 1],
            ['name' => 'رشت', 'status' => 1],
            ['name' => 'کرمان', 'status' => 1],
            ['name' => 'همدان', 'status' => 1],
            ['name' => 'یزد', 'status' => 1],
            ['name' => 'اردبیل', 'status' => 1],
            ['name' => 'بندرعباس', 'status' => 1],
            ['name' => 'اراک', 'status' => 1],
            ['name' => 'اسلام‌شهر', 'status' => 1],
            ['name' => 'زنجان', 'status' => 1],
            ['name' => 'سنندج', 'status' => 1],
            ['name' => 'بوشهر', 'status' => 1],
            ['name' => 'گرگان', 'status' => 1],
            ['name' => 'ساری', 'status' => 1],
            ['name' => 'بابول', 'status' => 1],
            ['name' => 'قزوین', 'status' => 1],
            ['name' => 'خرم‌آباد', 'status' => 1],
            ['name' => 'ایلام', 'status' => 1],
            ['name' => 'بیرجند', 'status' => 1],
            ['name' => 'بجنورد', 'status' => 1],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
