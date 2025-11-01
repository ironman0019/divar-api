<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CitySeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            CategoryAttributeSeeder::class,
            CategoryValueSeeder::class,
            AdvertisementSeeder::class,
            PromotionPriceSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
