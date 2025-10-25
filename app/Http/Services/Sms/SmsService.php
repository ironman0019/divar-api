<?php

namespace App\Http\Services\Sms;

use SoapClient;
use SoapFault;

class SmsService {

    private $username;
    private $password;
    private $from;
    private $patternId;

    public function __construct()
    {
        $this->username = config('sms.username');
        $this->password = config('sms.password');
        $this->from = config('sms.from');
        $this->patternId = config('sms.pattern_id');
    }

    public function SendOtpSms($phoneNumber, $otpCode)
    {
        try {
            $client = new SoapClient("http://api.payamak-panel.com/post/Send.asmx?wsdl", array('encoding' => 'UTF-8'));

            $parameters = [
                'username' => $this->username,
                'password' => $this->password,
                'text' => $otpCode,
                'to' => $phoneNumber,
                'bodyId' => (int)$this->patternId
            ];

            $result = $client->SendByBaseNumber2($parameters);
            $responseCode = $result->SendByBaseNumber2Result;

            if ($responseCode > 15) {
                return true;
            } else {
                $errorMessage = $this->getErrorMessage($responseCode);
                throw new \Exception($errorMessage);
            }

        } catch (SoapFault $e) {
            throw new \Exception('خطا در اتصال به سرویس پیامک: ' . $e->getMessage());
        }
    }

    private function getErrorMessage($errorCode)
    {
        switch($errorCode) {
            case 0: return 'نام کاربری یا رمز عبور صحیح نمی باشد';
            case 1: return 'دسترسی برای استفاده از این وبسرویس غیرفعال است';
            case 2: return 'اعتبار کافی نمی باشد';
            case 3: return 'خط ارسالی در سیستم تعریف نشده است';
            case 4: return 'کد متن ارسالی صحیح نمی باشد یا تایید نشده است';
            case 5: return 'متن ارسالی با متغیرهای مشخص شده همخوانی ندارد';
            case 6: return 'خطای داخلی رخ داده است';
            case 7: return 'متن حاوی کلمه فیلتر شده می باشد';
            case 10: return 'ممنوعیت ارسال لینک در متغیرها';
            case 11: return 'ارسال نشده';
            case 12: return 'مدارک کاربر کامل نمی باشد';
            default: return "خطا در ارسال پیامک (کد خطا: {$errorCode})";
        }
    }


    // public function OrderStatus($phoneNumber, $orderStatus)
    // {

    //     switch ($orderStatus) {
    //         case 0:
    //             $text =  'سفارش شما ثبت و در انتظار پرداخت است';
    //         case 2:
    //             $text =  'سفارش شما پرداخت و تکمیل شد به داشبورد کاربری خود مراجعه کنید.';
    //         case 3:
    //             $text =  'سفارش شما لغو شد.';
    //         default:
    //             $text =  '';
    //     }

    //     try {

    //         $client = new SoapClient("http://api.payamak-panel.com/post/Send.asmx?wsdl", array('encoding' => 'UTF-8'));

    //         $parameters = [
    //             'username' => $this->username,
    //             'password' => $this->password,
    //             'from' => $this->from,
    //             'to' => $phoneNumber,
    //             'text' => $text,
    //             'isflash' => false
    //         ];

    //         $result = $client->SendSimpleSms2($parameters);

    //         if(isset($resault->SendSimpleSms2Result)) {
    //             $responseCode = $result->SendSimpleSms2Result;
    //             if($responseCode == 0) {
    //                 return true;
    //             } else {
    //                 throw new \Exception('Error in sending sms!');
    //             }
    //         }

    //     } catch (SoapFault $e) {
    //         throw new \Exception('Error in sending sms');
    //     }
    // }

}
