<?php

namespace Tests\Feature;

use App\Models\Advertisement\Advertisement;
use App\Models\Category\Category;
use App\Models\City;
use App\Models\User;
use App\Support\Slugger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvertisementSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_slug_automatically_from_english_title(): void
    {
        $advertisement = $this->createAdvertisement([
            'title' => 'iPhone 13 Pro Max',
        ]);

        $this->assertNotEmpty($advertisement->slug);
        $this->assertSame('iPhone-13-Pro-Max', $advertisement->slug);
        $this->assertSame(Slugger::make('iPhone 13 Pro Max'), $advertisement->slug);
    }

    public function test_it_generates_slug_automatically_from_persian_title(): void
    {
        $advertisement = $this->createAdvertisement([
            'title' => 'آیفون ۱۳ پرو مکس',
        ]);

        $this->assertNotEmpty($advertisement->slug);
        $this->assertSame('آیفون-۱۳-پرو-مکس', $advertisement->slug);
        $this->assertSame(Slugger::make('آیفون ۱۳ پرو مکس'), $advertisement->slug);
    }

    public function test_it_generates_unique_slugs_for_duplicate_titles(): void
    {
        $first = $this->createAdvertisement([
            'title' => 'Samsung Galaxy S24',
        ]);

        $second = $this->createAdvertisement([
            'title' => 'Samsung Galaxy S24',
        ]);

        $this->assertSame('Samsung-Galaxy-S24', $first->slug);
        $this->assertSame('Samsung-Galaxy-S24-2', $second->slug);
        $this->assertNotSame($first->slug, $second->slug);
    }

    public function test_it_generates_unique_slugs_for_duplicate_persian_titles(): void
    {
        $first = $this->createAdvertisement([
            'title' => 'لپ تاپ ایسوس',
        ]);

        $second = $this->createAdvertisement([
            'title' => 'لپ تاپ ایسوس',
        ]);

        $this->assertSame('لپ-تاپ-ایسوس', $first->slug);
        $this->assertSame('لپ-تاپ-ایسوس-2', $second->slug);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createAdvertisement(array $overrides = []): Advertisement
    {
        $city = City::create([
            'name' => 'تهران',
            'status' => 1,
        ]);

        $user = User::factory()->create([
            'mobile' => '09'.fake()->unique()->numerify('#########'),
            'city_id' => $city->id,
            'is_active' => 1,
        ]);

        $category = Category::create([
            'name' => 'موبایل',
            'status' => 1,
        ]);

        return Advertisement::create(array_merge([
            'title' => 'Sample Ad',
            'description' => 'Test advertisement description',
            'ads_type' => 'sell',
            'ads_status' => 'good',
            'category_id' => $category->id,
            'city_id' => $city->id,
            'user_id' => $user->id,
            'status' => 3,
        ], $overrides));
    }
}
