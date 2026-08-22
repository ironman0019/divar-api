<?php

namespace App\Http\Services\Sms;

use SoapClient;
use SoapFault;

class SmsService
{
    private string $username;

    private string $password;

    private string $from;

    public function __construct()
    {
        $this->username = (string) config('sms.username');
        $this->password = (string) config('sms.password');
        $this->from = (string) config('sms.from');
    }

    public function SendOtpSms($phoneNumber, $otpCode)
    {
        if ($this->username === '' || $this->password === '') {
            throw new \Exception('تنظیمات پیامک ناقص است: SMS_USERNAME یا SMS_PASSWORD خالی است');
        }

        if ($this->from === '') {
            throw new \Exception('تنظیمات پیامک ناقص است: SMS_FROM (شماره خط اختصاصی) خالی است');
        }

        $text = 'کد تایید شما: '.$otpCode;

        try {
            $client = new SoapClient('http://api.payamak-panel.com/post/Send.asmx?wsdl', ['encoding' => 'UTF-8']);

            $parameters = [
                'username' => $this->username,
                'password' => $this->password,
                'from' => $this->from,
                'to' => $phoneNumber,
                'text' => $text,
                'isflash' => false,
            ];

            $result = $client->SendSimpleSMS2($parameters);
            $responseCode = $result->SendSimpleSMS2Result;

            // Success returns a long recId (> 15 digits). Errors are small ints.
            if (is_numeric($responseCode) && (int) $responseCode > 15) {
                return true;
            }

            throw new \Exception($this->getErrorMessage((int) $responseCode));
        } catch (SoapFault $e) {
            throw new \Exception('خطا در اتصال به سرویس پیامک: '.$e->getMessage());
        }
    }

    private function getErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            0 => 'نام کاربری یا رمز عبور صحیح نمی‌باشد',
            1 => 'دسترسی برای استفاده از این وبسرویس غیرفعال است',
            2 => 'اعتبار کافی نمی‌باشد',
            3 => 'محدودیت در ارسال روزانه',
            4 => 'محدودیت در حجم ارسال',
            5 => 'شماره فرستنده (SMS_FROM) در سیستم تعریف نشده است',
            6 => 'سامانه در حال بروزرسانی است',
            7 => 'متن حاوی کلمه فیلتر شده می‌باشد',
            9 => 'ارسال از خطوط عمومی از طریق وبسرویس امکان‌پذیر نمی‌باشد',
            10 => 'کاربر مورد نظر فعال نمی‌باشد',
            11 => 'ارسال نشده (مثلاً گیرنده در لیست سیاه مخابرات)',
            12 => 'مدارک کاربر کامل نمی‌باشد',
            14 => 'متن حاوی لینک می‌باشد',
            15 => 'ارسال به بیش از یک شماره بدون «لغو11» ممکن نیست',
            16 => 'شماره گیرنده‌ای یافت نشد',
            17 => 'متن پیامک خالی است',
            default => "خطا در ارسال پیامک (کد خطا: {$errorCode})",
        };
    }
}
