<?php

namespace App\Jobs;

use App\Http\Services\Sms\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendOtpSmsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public string $mobile,
        public string $otpCode
    ) {}

    public function handle(SmsService $smsService): void
    {
        $smsService->SendOtpSms($this->mobile, $this->otpCode);
    }
}
