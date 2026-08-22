<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\Otp\OtpException;
use App\Http\Services\Otp\OtpService;
use App\Jobs\SendOtpSmsJob;
use App\Models\User;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OTPLoginController extends Controller
{
    use HttpResponse;

    public function __construct(private OtpService $otpService) {}

    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^09[0-9]{9}$/',
        ]);

        $mobile = $this->formatMobileNumber($request->mobile);

        try {
            $user = User::where('mobile', $mobile)->first();
            $result = $this->otpService->createLogin($user?->id, $mobile);

            if (!empty($result['existing_otp'])) {
                return $this->success([
                    'mobile' => $mobile,
                    'token' => $result['token'],
                    'expires_in' => $result['expires_in'] ?? 120,
                    'cooldown' => $result['remaining_time'] ?? 0,
                    'existing_otp' => true,
                ], 'Existing OTP found');
            }

            SendOtpSmsJob::dispatch($mobile, $result['otp_code']);

            return $this->success([
                'mobile' => $mobile,
                'token' => $result['token'],
                'expires_in' => $result['expires_in'],
            ], 'OTP sent via SMS');
        } catch (OtpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->statusCode);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:4',
            'token' => 'required|string',
        ]);

        try {
            $payload = $this->otpService->verifyLogin($request->input('token'), $request->input('otp'));
            $user = !empty($payload['user_id'])
                ? User::find($payload['user_id'])
                : null;

            if (!$user) {
                $user = User::create([
                    'mobile' => $payload['mobile'],
                    'name' => null,
                    'password' => bcrypt(Str::random(32)),
                    'is_active' => 1,
                    'is_admin' => 0,
                ]);
            } else {
                $user->update(['is_active' => 1]);
            }

            $authToken = $user->createToken('auth_token')->plainTextToken;

            return $this->success([
                'token' => $authToken,
                'user' => $user->fresh(),
            ], 'OTP verified successfully');
        } catch (OtpException $e) {
            return $this->failed('', $e->getMessage(), $e->statusCode);
        }
    }

    public function checkCooldown(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^09[0-9]{9}$/',
        ]);

        $mobile = $this->formatMobileNumber($request->mobile);
        $user = User::where('mobile', $mobile)->first();
        $cooldown = $this->otpService->getCooldownRemaining('login', $mobile, $user?->id);

        if ($cooldown > 0) {
            return $this->success([
                'cooldown' => $cooldown,
                'can_send' => false,
            ], 'Cooldown active');
        }

        return $this->success([
            'cooldown' => 0,
            'can_send' => true,
        ], 'Can send OTP');
    }

    private function formatMobileNumber($mobile)
    {
        $persianToEnglish = [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ];

        $mobile = strtr($mobile, $persianToEnglish);
        $mobile = preg_replace('/[^0-9+]/', '', $mobile);

        if (str_starts_with($mobile, '+98')) {
            $mobile = '0'.substr($mobile, 3);
        } elseif (str_starts_with($mobile, '98')) {
            $mobile = '0'.substr($mobile, 2);
        } elseif (str_starts_with($mobile, '9')) {
            $mobile = '0'.$mobile;
        } elseif (!str_starts_with($mobile, '09') && strlen($mobile) === 10) {
            $mobile = '09'.$mobile;
        }

        return $mobile;
    }
}
