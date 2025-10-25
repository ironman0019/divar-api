<?php

namespace App\Http\Controllers\V1\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\User\Otp;
use Illuminate\Support\Str;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Services\Sms\SmsService;

class AuthController extends Controller
{
    use HttpResponse;
    private $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function registerSendOtp(Request $request)
    {
        $formFields = $request->validate([
            'name' => 'required|max:120|min:2|regex:/^[ا-یa-zA-Z0-9\-۰-۹ء-ي., ]+$/u',
            'email' => 'nullable|email|unique:users,email',
            'mobile' => 'required|numeric|unique:users,mobile|regex:/^09[0-9]{9}$/',
            'password' => 'required|confirmed|min:6'
        ]);

        // Format mobile number
        $formFields['mobile'] = $this->formatMobileNumber($formFields['mobile']);

        // check if an OTP already exists and still valid
        $lastOtp = Otp::where('mobile', $formFields['mobile'])
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if ($lastOtp) {
            $remaining = Carbon::parse($lastOtp->expires_at)->diffInSeconds(now());
            return response()->json([
                'message' => "Please wait $remaining seconds before requesting a new OTP"
            ], 429);
        }

        // Check last OTP request time (1 minute cooldown)
        $lastRequest = Otp::where('mobile', $formFields['mobile'])
            ->latest()
            ->first();
        if ($lastRequest) {
            $secondsPassed = now()->timestamp - $lastRequest->created_at->timestamp;
            if ($secondsPassed < 60) {
                $remaining = 60 - $secondsPassed;
                return response()->json([
                    'message' => "Please wait $remaining seconds before requesting a new OTP"
                ], 429);
            }
        }

        Otp::where('mobile', $formFields['mobile'])->delete();

        $otp = rand(1000, 9999);

        Otp::create([
            'mobile'     => $formFields['mobile'],
            'otp_code'   => $otp,
            'token'      => Str::random(32),
            'form_fields' => json_encode($formFields),
            'expires_at' => now()->addMinutes(2)
        ]);

        try {
            $this->smsService->SendOtpSms($formFields['mobile'], $otp);
            return $this->success([
                'mobile' => $formFields['mobile'],
                'expires_in' => 120
            ], "OTP sent via SMS");
        } catch (\Exception $e) {
            return $this->failed("", "خطا در ارسال کد", 500);
        }
    }


    public function registerVerifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^09[0-9]{9}$/',
            'otp'    => 'required|digits:4',
        ]);

        $mobile = $this->formatMobileNumber($request->mobile);
        $userOtp = Otp::where('mobile', $mobile)->first();

        if (!$userOtp) {
            return $this->failed("", "کد تایید نامعتبر است", 400);
        }

        if ($userOtp->attempts >= 3) {
            return $this->failed("", "تعداد تلاش‌های شما بیش از حد مجاز است. لطفاً کد جدید درخواست کنید", 429);
        }

        if ($userOtp->expires_at < Carbon::now()) {
            return $this->failed("", "کد تایید منقضی شده است", 400);
        }

        if ($userOtp->otp_code != $request->otp) {
            $userOtp->increment('attempts');
            $remainingAttempts = 3 - $userOtp->attempts;
            if ($remainingAttempts > 0) {
                return $this->failed("", "کد تایید اشتباه است. {$remainingAttempts} تلاش باقی مانده", 400);
            } else {
                return $this->failed("", "کد تایید اشتباه است. لطفاً کد جدید درخواست کنید", 400);
            }
        }

        // Registre user

        $formFields = json_decode($userOtp->form_fields, true);
        $formFields['password'] = Hash::make($formFields['password']);

        try {
            $user = DB::transaction(function () use ($formFields) {
                // Ensure is_admin is always 0 for new users
                $formFields['is_admin'] = 0;
                $user = User::create($formFields);
                return $user;
            });

            Otp::where('mobile', $mobile)->delete();

            return $this->success([
                'user' => $user,
                'token' => $user->createToken('Api Token')->plainTextToken
            ]);
        } catch (\Throwable $e) {
            Log::error('User creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->failed("", "User registeration failed", 500);
        }
    }


    public function login(Request $request)
    {
        $formFields = $request->validate([
            'mobile' => 'required|numeric|regex:/^09[0-9]{9}$/',
            'password' => 'required'
        ]);

        $formFields['mobile'] = $this->formatMobileNumber($formFields['mobile']);

        if (!Auth::attempt($formFields)) {
            return $this->failed("", "Invalid cardentials", 401);
        }

        $user = User::where('mobile', $formFields['mobile'])->first();

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('Api Token')->plainTextToken
        ]);
    }


    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success("", 'You have been logged out successfully!');
    }


    public function resetPasswordSendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|numeric|exists:users,mobile|regex:/^09[0-9]{9}$/'
        ]);

        $mobile = $this->formatMobileNumber($request->mobile);
        $user = User::where('mobile', $mobile)->first();

        $activeOtp = Otp::where('user_id', $user->id)
            ->where('used', 0)
            ->where('expires_at', '>', Carbon::now())
            ->first();
        if ($activeOtp) {
            $remaining = Carbon::parse($activeOtp->expires_at)->diffInSeconds(Carbon::now());
            throw new \Exception("You must wait $remaining seconds before requesting a new OTP", 429);
        }

        // Check last OTP request time (1 minute cooldown)
        $lastOtp = Otp::where('user_id', $user->id)
            ->latest()
            ->first();
        if ($lastOtp) {
            $secondsPassed = Carbon::now()->timestamp - $lastOtp->created_at->timestamp;
            if ($secondsPassed < 60) {
                $remaining = 60 - $secondsPassed;
                throw new \Exception("You must wait $remaining seconds before requesting a new OTP", 429);
            }
        }

        Otp::where('user_id', $user->id)->delete();

        // Generate OTP
        $otpCode = rand(1000, 9999);
        $token   = Str::random(32);

        $otp = Otp::create([
            'user_id'   => $user->id,
            'token'     => $token,
            'otp_code'  => $otpCode,
            'used'      => 0,
            'attempts'  => 0,
            'expires_at' => now()->addMinutes(2),
        ]);

        try {
            $this->smsService->SendOtpSms($user->mobile, $otpCode);
            return $this->success([
                'mobile' => $user->mobile,
                'token' => $otp->token,
                'expires_in' => 120
            ], "OTP sent via SMS");
        } catch (\Exception $e) {
            return $this->failed("", "خطا در ارسال کد", 500);
        }
    }

    public function resetPasswordVerifyOtp(Request $request)
    {
        $request->validate([
            'otp'   => 'required|digits:4',
            'token' => 'required|string',
            'password' => 'required|confirmed|min:6'
        ]);

        $otp = Otp::where('token', $request->input('token'))->first();

        if (!$otp || $otp->used) {
            return $this->failed("", "کد تایید نامعتبر است", 400);
        }

        if ($otp->expires_at < Carbon::now()) {
            return $this->failed("", "کد تایید منقضی شده است", 400);
        }

        if ($otp->attempts >= 3) {
            return $this->failed("", "تعداد تلاش‌های شما بیش از حد مجاز است. لطفاً کد جدید درخواست کنید", 429);
        }

        if ($otp->otp_code != $request->input('otp')) {
            $otp->increment('attempts');
            $remainingAttempts = 3 - $otp->attempts;
            if ($remainingAttempts > 0) {
                return $this->failed("", "کد تایید اشتباه است. {$remainingAttempts} تلاش باقی مانده", 400);
            } else {
                return $this->failed("", "کد تایید اشتباه است. لطفاً کد جدید درخواست کنید", 400);
            }
        }

        // Mark OTP as used
        $otp->update(['used' => 1]);

        $user = $otp->user;
        if (!$user) {
            return $this->failed("", "User not found!", 404);
        }

        $password = Hash::make($request->password);
        $user->update(['password' => $password]);

        return $this->success("", "password changed successfully");
    }

    /**
     * Format mobile number to standard Iranian format (09xxxxxxxxx)
     */
    private function formatMobileNumber($mobile)
    {
        // Convert Persian digits to English
        $persianToEnglish = [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9'
        ];

        $mobile = strtr($mobile, $persianToEnglish);

        // Remove all non-numeric characters except +
        $mobile = preg_replace('/[^0-9+]/', '', $mobile);

        // Handle different formats
        if (str_starts_with($mobile, '+98')) {
            // +989123456789 -> 09123456789
            $mobile = '0' . substr($mobile, 3);
        } elseif (str_starts_with($mobile, '98')) {
            // 989123456789 -> 09123456789
            $mobile = '0' . substr($mobile, 2);
        } elseif (str_starts_with($mobile, '9')) {
            // 9123456789 -> 09123456789
            $mobile = '0' . $mobile;
        } elseif (!str_starts_with($mobile, '09')) {
            // If it doesn't start with 09, assume it needs 09 prefix
            if (strlen($mobile) === 10) {
                $mobile = '09' . $mobile;
            }
        }

        return $mobile;
    }
}
