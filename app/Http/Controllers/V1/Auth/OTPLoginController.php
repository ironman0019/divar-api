<?php

namespace App\Http\Controllers\V1\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\User\Otp;
use Illuminate\Support\Str;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Services\Sms\SmsService;

class OTPLoginController extends Controller
{
    use HttpResponse;
    private $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^09[0-9]{9}$/',
        ]);

        $mobile = $this->formatMobileNumber($request->mobile);

        try {
            $result = DB::transaction(function () use ($mobile) {
                // Check if user exists
                $user = User::where('mobile', $mobile)->first();

                if (!$user) {
                    // Check cooldown for new users (by mobile)
                    $lastOtp = Otp::where('mobile', $mobile)
                        ->latest()
                        ->first();
                    if ($lastOtp) {
                        $secondsPassed = Carbon::now()->timestamp - $lastOtp->created_at->timestamp;
                        if ($secondsPassed < 60) {
                            $remaining = 60 - $secondsPassed;
                            // Return existing OTP info instead of throwing error
                            return [
                                'existing_otp' => true,
                                'token' => $lastOtp->token,
                                'remaining_time' => $remaining,
                                'expires_at' => $lastOtp->expires_at
                            ];
                        }
                    }

                    // Don't create user until OTP is verified
                    // Just create OTP record with mobile number
                    $otpCode = rand(1000, 9999);
                    $token   = Str::random(32);

                    $otp = Otp::create([
                        'mobile'     => $mobile,
                        'token'      => $token,
                        'otp_code'   => $otpCode,
                        'used'       => 0,
                        'attempts'   => 0,
                        'expires_at' => now()->addMinutes(2),
                    ]);

                    return [$otp, $otpCode];
                }

                // Check active OTP for existing user
                $activeOtp = Otp::where('user_id', $user->id)
                    ->where('used', 0)
                    ->where('expires_at', '>', Carbon::now())
                    ->first();
                if ($activeOtp) {
                    $remaining = Carbon::parse($activeOtp->expires_at)->diffInSeconds(Carbon::now());
                    // Return existing OTP info instead of throwing error
                    return [
                        'existing_otp' => true,
                        'token' => $activeOtp->token,
                        'remaining_time' => $remaining,
                        'expires_at' => $activeOtp->expires_at
                    ];
                }

                // Check last OTP request time (1 minute cooldown)
                $lastOtp = Otp::where('user_id', $user->id)
                    ->latest()
                    ->first();
                if ($lastOtp) {
                    $secondsPassed = Carbon::now()->timestamp - $lastOtp->created_at->timestamp;
                    if ($secondsPassed < 60) {
                        $remaining = 60 - $secondsPassed;
                        // Return existing OTP info instead of throwing error
                        return [
                            'existing_otp' => true,
                            'token' => $lastOtp->token,
                            'remaining_time' => $remaining,
                            'expires_at' => $lastOtp->expires_at
                        ];
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

                return [$user, $otp, $otpCode];
            });

            // Handle different return types
            if (isset($result['existing_otp'])) {
                // Existing OTP case - return existing token with cooldown info
                return $this->success([
                    'mobile' => $mobile,
                    'token' => $result['token'],
                    'expires_in' => Carbon::parse($result['expires_at'])->diffInSeconds(Carbon::now()),
                    'cooldown' => $result['remaining_time'],
                    'existing_otp' => true
                ], "Existing OTP found");
            } elseif (count($result) === 2) {
                // New user case - no user created yet
                [$otp, $otpCode] = $result;

                try {
                    $this->smsService->SendOtpSms($mobile, $otpCode);
                    return $this->success([
                        'mobile' => $mobile,
                        'token' => $otp->token,
                        'expires_in' => 120
                    ], "OTP sent via SMS");
                } catch (\Exception $e) {
                    return $this->failed("", "خطا در ارسال کد", 500);
                }
            } else {
                // Existing user case
                [$user, $otp, $otpCode] = $result;

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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() === 429 ? 429 : 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp'   => 'required|digits:4',
            'token' => 'required|string',
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

        // If no user exists, create one now
        if (!$user) {
            $user = User::create([
                'mobile' => $otp->mobile,
                'name' => null,
                'password' => bcrypt(Str::random(32)), // Generate random hashed password
                'is_active' => 1, // Active after OTP verification
                'is_admin' => 0 // Ensure user is not admin
            ]);
        } else {
            // Activate existing user after OTP verification
            $user->update(['is_active' => 1]);
        }

        // Issue Sanctum token
        $authToken = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'token' => $authToken,
            'user' => $user->fresh()
        ], "OTP verified successfully");
    }

    public function checkCooldown(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^09[0-9]{9}$/',
        ]);

        $mobile = $this->formatMobileNumber($request->mobile);

        // Check if user exists
        $user = User::where('mobile', $mobile)->first();

        if (!$user) {
            // Check cooldown for new users (by mobile)
            $lastOtp = Otp::where('mobile', $mobile)
                ->latest()
                ->first();
            if ($lastOtp) {
                $secondsPassed = Carbon::now()->timestamp - $lastOtp->created_at->timestamp;
                if ($secondsPassed < 60) {
                    $remaining = 60 - $secondsPassed;
                    return $this->success([
                        'cooldown' => $remaining,
                        'can_send' => false
                    ], "Cooldown active");
                }
            }
        } else {
            // Check cooldown for existing users
            $lastOtp = Otp::where('user_id', $user->id)
                ->latest()
                ->first();
            if ($lastOtp) {
                $secondsPassed = Carbon::now()->timestamp - $lastOtp->created_at->timestamp;
                if ($secondsPassed < 60) {
                    $remaining = 60 - $secondsPassed;
                    return $this->success([
                        'cooldown' => $remaining,
                        'can_send' => false
                    ], "Cooldown active");
                }
            }
        }

        return $this->success([
            'cooldown' => 0,
            'can_send' => true
        ], "Can send OTP");
    }

    /**
     * Format mobile number to standard Iranian format (09xxxxxxxxx)
     */
    private function formatMobileNumber($mobile)
    {
        // Convert Persian digits to English
        $persianToEnglish = [
            '۰' => '0',
            '۱' => '1',
            '۲' => '2',
            '۳' => '3',
            '۴' => '4',
            '۵' => '5',
            '۶' => '6',
            '۷' => '7',
            '۸' => '8',
            '۹' => '9',
            '٠' => '0',
            '١' => '1',
            '٢' => '2',
            '٣' => '3',
            '٤' => '4',
            '٥' => '5',
            '٦' => '6',
            '٧' => '7',
            '٨' => '8',
            '٩' => '9'
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
