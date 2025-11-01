<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\User;
use App\Models\Advertisement\Advertisement;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $advertisements = Advertisement::where('status', 2)->get(); // Only active advertisements

        if ($users->isEmpty() || $advertisements->isEmpty()) {
            $this->command->warn('No users or active advertisements found. Please run UserSeeder and AdvertisementSeeder first.');
            return;
        }

        // Persian descriptions for different payment types
        $ladderDescriptions = [
            'پرداخت تبلیغ نردبانی برای آگهی - افزایش بازدید و نمایش بهتر',
            'ارتقا آگهی با استفاده از نردبان - نمایش در صفحات اول',
            'تبلیغ نردبانی به مدت مشخص شده - بهبود رتبه در نتایج جستجو',
            'پرداخت برای تبلیغ نردبانی آگهی',
        ];

        $specialDescriptions = [
            'پرداخت تبلیغ ویژه برای آگهی - نمایش در بخش آگهی‌های ویژه',
            'تبلیغ ویژه آگهی - افزایش شانس مشاهده و تماس',
            'پرداخت برای نمایش آگهی در بخش ویژه',
            'تبلیغ ویژه با امکانات بیشتر - نمایش برتر در سایت',
        ];

        // Create payments with different statuses and types
        $totalPayments = 50;
        $statuses = [Payment::STATUS_PAID, Payment::STATUS_PAID, Payment::STATUS_PAID, Payment::STATUS_PENDING, Payment::STATUS_FAILED];
        $paymentTypes = [Payment::TYPE_LADDER, Payment::TYPE_SPECIAL];
        $durationOptions = [7, 14, 30, 60, 90];

        // Price ranges (in Tomans)
        $priceRanges = [
            'ladder' => [
                'min' => 50000,
                'max' => 500000,
            ],
            'special' => [
                'min' => 100000,
                'max' => 2000000,
            ],
        ];

        for ($i = 0; $i < $totalPayments; $i++) {
            $user = $users->random();
            $advertisement = $advertisements->random();
            $paymentType = $paymentTypes[array_rand($paymentTypes)];
            $status = $statuses[array_rand($statuses)];
            $durationDays = $durationOptions[array_rand($durationOptions)];

            // Calculate amount based on type and duration (simple calculation)
            $basePrice = $priceRanges[$paymentType]['min'];
            $maxPrice = $priceRanges[$paymentType]['max'];
            $multiplier = ($durationDays / 30); // Price increases with duration
            $amount = rand(
                (int)($basePrice * $multiplier),
                (int)($maxPrice * $multiplier)
            );

            // Generate Persian description
            $descriptions = $paymentType === Payment::TYPE_LADDER ? $ladderDescriptions : $specialDescriptions;
            $description = $descriptions[array_rand($descriptions)] . ' - مدت: ' . $durationDays . ' روز';

            // Generate authority code (Zarinpal format: usually starts with A000...)
            $authority = 'A000' . str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);

            // Generate data based on status
            $refId = null;
            $cardPan = null;
            $traceNo = null;
            $gatewayResponse = null;
            $createdAt = Carbon::now()->subDays(rand(0, 90));

            if ($status === Payment::STATUS_PAID) {
                // For paid payments, generate complete transaction data
                $refId = str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);
                $cardPan = '6219' . str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);
                $traceNo = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
                $gatewayResponse = [
                    'status' => 100,
                    'message' => 'تراکنش با موفقیت انجام شد',
                    'refId' => $refId,
                    'cardPan' => substr($cardPan, -4),
                    'transactionDate' => $createdAt->toIso8601String(),
                ];
            } elseif ($status === Payment::STATUS_FAILED) {
                // For failed payments
                $failedReasons = [
                    'موجودی حساب کافی نیست',
                    'شماره کارت معتبر نیست',
                    'رمز کارت اشتباه است',
                    'تراکنش توسط کاربر لغو شد',
                    'خطا در اتصال به درگاه پرداخت',
                ];
                $gatewayResponse = [
                    'status' => rand(-1, -20),
                    'message' => $failedReasons[array_rand($failedReasons)],
                    'errorCode' => rand(100, 999),
                ];
            }

            Payment::create([
                'user_id' => $user->id,
                'advertisement_id' => $advertisement->id,
                'amount' => $amount,
                'payment_type' => $paymentType,
                'duration_days' => $durationDays,
                'description' => $description,
                'status' => $status,
                'authority' => $authority,
                'ref_id' => $refId,
                'card_pan' => $cardPan,
                'trace_no' => $traceNo,
                'gateway_response' => $gatewayResponse,
                'created_at' => $createdAt,
                'updated_at' => $status === Payment::STATUS_PAID 
                    ? $createdAt->copy()->addMinutes(rand(1, 30))
                    : $createdAt,
            ]);
        }

        $this->command->info("Created {$totalPayments} payment records with Persian data.");
    }
}

