<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'احمد محمدی',
                'email' => 'ahmad@example.com',
                'mobile' => '09123456789',
                'password' => Hash::make('password'),
                'city_id' => 1, // تهران
                'is_active' => 1,
            ],
            [
                'name' => 'فاطمه احمدی',
                'email' => 'fateme@example.com',
                'mobile' => '09123456790',
                'password' => Hash::make('password'),
                'city_id' => 2, // مشهد
                'is_active' => 1,
            ],
            [
                'name' => 'علی رضایی',
                'email' => 'ali@example.com',
                'mobile' => '09123456791',
                'password' => Hash::make('password'),
                'city_id' => 3, // اصفهان
                'is_active' => 1,
            ],
            [
                'name' => 'زهرا کریمی',
                'email' => 'zahra@example.com',
                'mobile' => '09123456792',
                'password' => Hash::make('password'),
                'city_id' => 4, // شیراز
                'is_active' => 1,
            ],
            [
                'name' => 'محمد حسینی',
                'email' => 'mohammad@example.com',
                'mobile' => '09123456793',
                'password' => Hash::make('password'),
                'city_id' => 5, // تبریز
                'is_active' => 1,
            ],
            [
                'name' => 'مریم نوری',
                'email' => 'maryam@example.com',
                'mobile' => '09123456794',
                'password' => Hash::make('password'),
                'city_id' => 1, // تهران
                'is_active' => 1,
            ],
            [
                'name' => 'حسن صادقی',
                'email' => 'hasan@example.com',
                'mobile' => '09123456795',
                'password' => Hash::make('password'),
                'city_id' => 2, // مشهد
                'is_active' => 1,
            ],
            [
                'name' => 'نرگس مظفری',
                'email' => 'narges@example.com',
                'mobile' => '09123456796',
                'password' => Hash::make('password'),
                'city_id' => 3, // اصفهان
                'is_active' => 1,
            ],
            [
                'name' => 'رضا امینی',
                'email' => 'reza@example.com',
                'mobile' => '09123456797',
                'password' => Hash::make('password'),
                'city_id' => 4, // شیراز
                'is_active' => 1,
            ],
            [
                'name' => 'سارا قاسمی',
                'email' => 'sara@example.com',
                'mobile' => '09123456798',
                'password' => Hash::make('password'),
                'city_id' => 5, // تبریز
                'is_active' => 1,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
