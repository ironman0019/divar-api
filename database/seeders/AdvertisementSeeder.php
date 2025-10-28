<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Advertisement\Advertisement;
use App\Models\Advertisement\Gallery;
use App\Models\Category\Category;
use App\Models\User;
use App\Models\City;
use Carbon\Carbon;

class AdvertisementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some categories and users for creating advertisements
        $apartmentCategory = Category::where('slug', 'apartment')->first();
        $carCategory = Category::where('slug', 'car')->first();
        $mobileCategory = Category::where('slug', 'mobile-tablet')->first();
        $laptopCategory = Category::where('slug', 'laptop-computer')->first();
        $kitchenCategory = Category::where('slug', 'kitchen-appliances')->first();

        $users = User::take(5)->get();
        $cities = City::take(5)->get();

        $advertisements = [
            // Apartment advertisements
            [
                'title' => 'آپارتمان 120 متری در تهرانپارس',
                'description' => 'آپارتمان 3 خوابه در منطقه تهرانپارس، نزدیک به مترو، دارای پارکینگ و انباری، مناسب برای خانواده',
                'ads_type' => 'sell',
                'ads_status' => 'excellent',
                'category_id' => $apartmentCategory->id,
                'city_id' => $cities[0]->id,
                'user_id' => $users[0]->id,
                'status' => 2,
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'expired_at' => Carbon::now()->addDays(30),
                'price' => 2500000000,
                'contact' => '09123456789',
                'tags' => 'آپارتمان,تهرانپارس,3خوابه,پارکینگ',
                'lat' => '35.7219',
                'lng' => '51.3347',
                'willing_to_trade' => false,
            ],
            [
                'title' => 'آپارتمان 90 متری در ونک',
                'description' => 'آپارتمان 2 خوابه در منطقه ونک، نزدیک به مراکز تجاری، دارای بالکن و نور مناسب',
                'ads_type' => 'rent',
                'ads_status' => 'good',
                'category_id' => $apartmentCategory->id,
                'city_id' => $cities[0]->id,
                'user_id' => $users[1]->id,
                'status' => 2,
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'expired_at' => Carbon::now()->addDays(30),
                'price' => 15000000,
                'contact' => '09123456790',
                'tags' => 'آپارتمان,ونک,2خوابه,اجاره',
                'lat' => '35.7219',
                'lng' => '51.3347',
                'willing_to_trade' => false,
            ],

            // Car advertisements
            [
                'title' => 'پژو 206 مدل 95',
                'description' => 'پژو 206 مدل 95، رنگ سفید، 120 هزار کیلومتر، گیربکس دستی، دوگانه‌سوز، در حد نو',
                'ads_type' => 'sell',
                'ads_status' => 'good',
                'category_id' => $carCategory->id,
                'city_id' => $cities[1]->id,
                'user_id' => $users[2]->id,
                'status' => 2,
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'expired_at' => Carbon::now()->addDays(30),
                'price' => 45000000,
                'contact' => '09123456791',
                'tags' => 'پژو206,خودرو,سفید,دوگانه‌سوز',
                'lat' => '36.2605',
                'lng' => '59.6168',
                'willing_to_trade' => true,
            ],
            [
                'title' => 'سمند EF7 مدل 98',
                'description' => 'سمند EF7 مدل 98، رنگ مشکی، 80 هزار کیلومتر، گیربکس اتوماتیک، بنزین سوز',
                'ads_type' => 'sell',
                'ads_status' => 'excellent',
                'category_id' => $carCategory->id,
                'city_id' => $cities[2]->id,
                'user_id' => $users[3]->id,
                'status' => 2,
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'expired_at' => Carbon::now()->addDays(30),
                'price' => 180000000,
                'contact' => '09123456792',
                'tags' => 'سمند,خودرو,مشکی,اتوماتیک',
                'lat' => '32.6546',
                'lng' => '51.6680',
                'willing_to_trade' => false,
            ],

            // Mobile advertisements
            [
                'title' => 'گوشی سامسونگ گلکسی S21',
                'description' => 'گوشی سامسونگ گلکسی S21، رنگ مشکی، حافظه 128 گیگابایت، رم 8 گیگابایت، در حد نو',
                'ads_type' => 'sell',
                'ads_status' => 'as_good_as_new',
                'category_id' => $mobileCategory->id,
                'city_id' => $cities[3]->id,
                'user_id' => $users[4]->id,
                'status' => 2,
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'expired_at' => Carbon::now()->addDays(30),
                'price' => 12000000,
                'contact' => '09123456793',
                'tags' => 'سامسونگ,گلکسی,موبایل,128گیگ',
                'lat' => '29.5918',
                'lng' => '52.5837',
                'willing_to_trade' => true,
            ],
            [
                'title' => 'آیفون 13 پرو',
                'description' => 'آیفون 13 پرو، رنگ طلایی، حافظه 256 گیگابایت، رم 6 گیگابایت، با گارانتی',
                'ads_type' => 'sell',
                'ads_status' => 'excellent',
                'category_id' => $mobileCategory->id,
                'city_id' => $cities[4]->id,
                'user_id' => $users[0]->id,
                'status' => 2,
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'expired_at' => Carbon::now()->addDays(30),
                'price' => 25000000,
                'contact' => '09123456794',
                'tags' => 'آیفون,13پرو,طلایی,256گیگ',
                'lat' => '38.0808',
                'lng' => '46.2919',
                'willing_to_trade' => false,
            ],

            // Laptop advertisements
            [
                'title' => 'لپ‌تاپ ایسوس ویووبوک',
                'description' => 'لپ‌تاپ ایسوس ویووبوک، پردازنده Intel i5، رم 8 گیگابایت، حافظه 512 گیگابایت SSD',
                'ads_type' => 'sell',
                'ads_status' => 'good',
                'category_id' => $laptopCategory->id,
                'city_id' => $cities[0]->id,
                'user_id' => $users[1]->id,
                'status' => 2,
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'expired_at' => Carbon::now()->addDays(30),
                'price' => 18000000,
                'contact' => '09123456795',
                'tags' => 'ایسوس,لپ‌تاپ,i5,512گیگ',
                'lat' => '35.7219',
                'lng' => '51.3347',
                'willing_to_trade' => true,
            ],

            // Kitchen appliances
            [
                'title' => 'یخچال سامسونگ دو درب',
                'description' => 'یخچال سامسونگ دو درب، مدل RT28K5070SL، ظرفیت 280 لیتر، رنگ نقره‌ای، در حد نو',
                'ads_type' => 'sell',
                'ads_status' => 'as_good_as_new',
                'category_id' => $kitchenCategory->id,
                'city_id' => $cities[1]->id,
                'user_id' => $users[2]->id,
                'status' => 2,
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'expired_at' => Carbon::now()->addDays(30),
                'price' => 8500000,
                'contact' => '09123456796',
                'tags' => 'یخچال,سامسونگ,دو درب,280لیتر',
                'lat' => '36.2605',
                'lng' => '59.6168',
                'willing_to_trade' => false,
            ],
            [
                'title' => 'ماشین لباسشویی ال‌جی',
                'description' => 'ماشین لباسشویی ال‌جی، ظرفیت 7 کیلوگرم، دارای برنامه‌های مختلف شستشو',
                'ads_type' => 'sell',
                'ads_status' => 'good',
                'category_id' => $kitchenCategory->id,
                'city_id' => $cities[2]->id,
                'user_id' => $users[3]->id,
                'status' => 2,
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'expired_at' => Carbon::now()->addDays(30),
                'price' => 6500000,
                'contact' => '09123456797',
                'tags' => 'لباسشویی,ال‌جی,7کیلو,برنامه‌دار',
                'lat' => '32.6546',
                'lng' => '51.6680',
                'willing_to_trade' => true,
            ],
        ];

        foreach ($advertisements as $adData) {
            $advertisement = Advertisement::create($adData);
            
            // Create some dummy gallery images for some advertisements
            if (rand(0, 1)) {
                $galleryImages = [
                    ['url' => '/upload/2024/01/15/sample1.jpg'],
                    ['url' => '/upload/2024/01/15/sample2.jpg'],
                ];
                
                foreach ($galleryImages as $galleryData) {
                    Gallery::create([
                        'advertisement_id' => $advertisement->id,
                        'url' => $galleryData['url'],
                    ]);
                }
            }
        }
    }
}
