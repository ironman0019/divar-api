<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\Otp\OtpException;
use App\Http\Services\Otp\OtpService;
use App\Jobs\SendOtpSmsJob;
use App\Models\User;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use HttpResponse;

    public function __construct(private OtpService $otpService) {}

    public function registerSendOtp(Request $request)
    {
        $formFields = $request->validate([
            'name' => 'required|max:120|min:2|regex:/^[ا-یa-zA-Z0-9\-۰-۹ء-ي., ]+$/u',
            'email' => 'nullable|email|unique:users,email',
            'mobile' => 'required|numeric|unique:users,mobile|regex:/^09[0-9]{9}$/',
            'password' => 'required|confirmed|min:6',
        ]);

        $formFields['mobile'] = $this->formatMobileNumber($formFields['mobile']);

        try {
            $result = $this->otpService->createRegister($formFields['mobile'], $formFields);
            SendOtpSmsJob::dispatch($formFields['mobile'], $result['otp_code']);

            return $this->success([
                'mobile' => $formFields['mobile'],
                'expires_in' => $result['expires_in'],
            ], 'OTP sent via SMS');
        } catch (OtpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->statusCode);
        } catch (\Exception $e) {
            return $this->failed('', 'خطا در ارسال کد', 500);
        }
    }

    public function registerVerifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^09[0-9]{9}$/',
            'otp' => 'required|digits:4',
        ]);

        $mobile = $this->formatMobileNumber($request->mobile);

        try {
            $formFields = $this->otpService->verifyRegister($mobile, $request->otp);
            $formFields['password'] = Hash::make($formFields['password']);

            $user = DB::transaction(function () use ($formFields) {
                $formFields['is_admin'] = 0;

                return User::create($formFields);
            });

            return $this->success([
                'user' => $user,
                'token' => $user->createToken('Api Token')->plainTextToken,
            ]);
        } catch (OtpException $e) {
            return $this->failed('', $e->getMessage(), $e->statusCode);
        } catch (\Throwable $e) {
            Log::error('User creation failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->failed('', 'User registeration failed', 500);
        }
    }

    public function login(Request $request)
    {
        $formFields = $request->validate([
            'mobile' => 'required|numeric|regex:/^09[0-9]{9}$/',
            'password' => 'required',
        ]);

        $formFields['mobile'] = $this->formatMobileNumber($formFields['mobile']);

        if (!Auth::attempt($formFields)) {
            return $this->failed('', 'Invalid cardentials', 401);
        }

        $user = User::where('mobile', $formFields['mobile'])->first();

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('Api Token')->plainTextToken,
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success('', 'You have been logged out successfully!');
    }

    public function resetPasswordSendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|numeric|exists:users,mobile|regex:/^09[0-9]{9}$/',
        ]);

        $mobile = $this->formatMobileNumber($request->mobile);
        $user = User::where('mobile', $mobile)->first();

        try {
            $result = $this->otpService->createReset($user->id, $mobile);
            SendOtpSmsJob::dispatch($user->mobile, $result['otp_code']);

            return $this->success([
                'mobile' => $user->mobile,
                'token' => $result['token'],
                'expires_in' => $result['expires_in'],
            ], 'OTP sent via SMS');
        } catch (OtpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->statusCode);
        } catch (\Exception $e) {
            return $this->failed('', 'خطا در ارسال کد', 500);
        }
    }

    public function resetPasswordVerifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:4',
            'token' => 'required|string',
            'password' => 'required|confirmed|min:6',
        ]);

        try {
            $userId = $this->otpService->verifyReset($request->input('token'), $request->input('otp'));
            $user = User::find($userId);

            if (!$user) {
                return $this->failed('', 'User not found!', 404);
            }

            $user->update(['password' => Hash::make($request->password)]);

            return $this->success('', 'password changed successfully');
        } catch (OtpException $e) {
            return $this->failed('', $e->getMessage(), $e->statusCode);
        }
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
