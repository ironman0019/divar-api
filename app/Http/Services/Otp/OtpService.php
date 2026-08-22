<?php

namespace App\Http\Services\Otp;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class OtpService
{
    public const TTL = 120;

    public const COOLDOWN = 60;

    public const MAX_ATTEMPTS = 3;

    public function createRegister(string $mobile, array $formFields): array
    {
        $this->assertCanSend('register', $mobile);

        $this->deletePurposeKeys('register', $mobile);

        return $this->storeOtp('register', $mobile, null, $formFields);
    }

    public function verifyRegister(string $mobile, string $code): array
    {
        $token = Redis::get($this->registerIndexKey($mobile));

        if (!$token) {
            throw new OtpException('کد تایید نامعتبر است', 400);
        }

        $payload = $this->verifyByToken($token, $code);

        $this->deletePurposeKeys('register', $mobile, $token);

        return $payload['form_fields'] ?? [];
    }

    public function createReset(int $userId, string $mobile): array
    {
        $this->assertCanSend('reset', $mobile, $userId);

        $this->deletePurposeKeys('reset', $mobile, null, $userId);

        return $this->storeOtp('reset', $mobile, $userId);
    }

    public function verifyReset(string $token, string $code): int
    {
        $payload = $this->verifyByToken($token, $code);

        if (($payload['purpose'] ?? '') !== 'reset' || empty($payload['user_id'])) {
            throw new OtpException('کد تایید نامعتبر است', 400);
        }

        $this->deletePurposeKeys('reset', $payload['mobile'], $token, (int) $payload['user_id']);

        return (int) $payload['user_id'];
    }

    public function createLogin(?int $userId, string $mobile): array
    {
        $existing = $this->getExistingOtp('login', $mobile, $userId);

        if ($existing) {
            return array_merge($existing, ['existing_otp' => true]);
        }

        $cooldown = $this->getCooldownRemaining('login', $mobile, $userId);

        if ($cooldown > 0) {
            $token = $this->resolveToken('login', $mobile, $userId);

            if ($token) {
                $expiresIn = max(Redis::ttl($this->dataKey($token)), 1);

                return [
                    'existing_otp' => true,
                    'token' => $token,
                    'remaining_time' => $cooldown,
                    'expires_at' => now()->addSeconds($expiresIn)->toIso8601String(),
                    'expires_in' => $expiresIn,
                ];
            }
        }

        $this->deletePurposeKeys('login', $mobile, null, $userId);

        return $this->storeOtp('login', $mobile, $userId);
    }

    public function verifyLogin(string $token, string $code): array
    {
        $payload = $this->verifyByToken($token, $code);

        if (($payload['purpose'] ?? '') !== 'login') {
            throw new OtpException('کد تایید نامعتبر است', 400);
        }

        $this->deletePurposeKeys(
            'login',
            $payload['mobile'],
            $token,
            isset($payload['user_id']) ? (int) $payload['user_id'] : null
        );

        return [
            'mobile' => $payload['mobile'],
            'user_id' => $payload['user_id'] ?? null,
        ];
    }

    public function getCooldownRemaining(string $purpose, string $mobile, ?int $userId = null): int
    {
        $ttl = Redis::ttl($this->cooldownKey($purpose, $mobile, $userId));

        return $ttl > 0 ? $ttl : 0;
    }

    public function getExistingOtp(string $purpose, string $mobile, ?int $userId = null): ?array
    {
        $token = $this->resolveToken($purpose, $mobile, $userId);

        if (!$token) {
            return null;
        }

        $payload = $this->getPayload($token);

        if (!$payload || ($payload['used'] ?? false)) {
            return null;
        }

        $remaining = Redis::ttl($this->dataKey($token));

        if ($remaining <= 0) {
            return null;
        }

        $cooldown = $this->getCooldownRemaining($purpose, $mobile, $userId);

        return [
            'token' => $token,
            'remaining_time' => max($cooldown, $remaining),
            'expires_at' => now()->addSeconds($remaining)->toIso8601String(),
            'expires_in' => $remaining,
        ];
    }

    protected function assertCanSend(string $purpose, string $mobile, ?int $userId = null): void
    {
        $existing = $this->getExistingOtp($purpose, $mobile, $userId);

        if ($existing) {
            throw new OtpException(
                "Please wait {$existing['remaining_time']} seconds before requesting a new OTP",
                429
            );
        }

        $cooldown = $this->getCooldownRemaining($purpose, $mobile, $userId);

        if ($cooldown > 0) {
            throw new OtpException(
                "Please wait {$cooldown} seconds before requesting a new OTP",
                429
            );
        }
    }

    protected function storeOtp(string $purpose, string $mobile, ?int $userId = null, ?array $formFields = null): array
    {
        $otpCode = (string) rand(1000, 9999);
        $token = Str::random(32);

        $payload = [
            'purpose' => $purpose,
            'mobile' => $mobile,
            'user_id' => $userId,
            'otp_code' => $otpCode,
            'token' => $token,
            'attempts' => 0,
            'used' => false,
            'form_fields' => $formFields,
        ];

        Redis::setex($this->dataKey($token), self::TTL, json_encode($payload));
        Redis::setex($this->tokenIndexKey($token), self::TTL, json_encode([
            'purpose' => $purpose,
            'mobile' => $mobile,
            'user_id' => $userId,
        ]));

        if ($purpose === 'register') {
            Redis::setex($this->registerIndexKey($mobile), self::TTL, $token);
        } elseif ($purpose === 'login') {
            Redis::setex($this->loginMobileIndexKey($mobile), self::TTL, $token);
            if ($userId) {
                Redis::setex($this->loginUserIndexKey($userId), self::TTL, $token);
            }
        } elseif ($purpose === 'reset' && $userId) {
            Redis::setex($this->resetUserIndexKey($userId), self::TTL, $token);
        }

        Redis::setex($this->cooldownKey($purpose, $mobile, $userId), self::COOLDOWN, '1');

        return [
            'token' => $token,
            'otp_code' => $otpCode,
            'expires_in' => self::TTL,
            'mobile' => $mobile,
        ];
    }

    protected function verifyByToken(string $token, string $code): array
    {
        $payload = $this->getPayload($token);

        if (!$payload || ($payload['used'] ?? false)) {
            throw new OtpException('کد تایید نامعتبر است', 400);
        }

        if (Redis::ttl($this->dataKey($token)) <= 0) {
            throw new OtpException('کد تایید منقضی شده است', 400);
        }

        if (($payload['attempts'] ?? 0) >= self::MAX_ATTEMPTS) {
            throw new OtpException('تعداد تلاش‌های شما بیش از حد مجاز است. لطفاً کد جدید درخواست کنید', 429);
        }

        if (($payload['otp_code'] ?? '') !== $code) {
            $payload['attempts'] = ($payload['attempts'] ?? 0) + 1;
            $ttl = max(Redis::ttl($this->dataKey($token)), 1);
            Redis::setex($this->dataKey($token), $ttl, json_encode($payload));

            $remainingAttempts = self::MAX_ATTEMPTS - $payload['attempts'];

            if ($remainingAttempts > 0) {
                throw new OtpException("کد تایید اشتباه است. {$remainingAttempts} تلاش باقی مانده", 400);
            }

            throw new OtpException('کد تایید اشتباه است. لطفاً کد جدید درخواست کنید', 400);
        }

        return $payload;
    }

    protected function getPayload(string $token): ?array
    {
        $raw = Redis::get($this->dataKey($token));

        return $raw ? json_decode($raw, true) : null;
    }

    protected function resolveToken(string $purpose, string $mobile, ?int $userId = null): ?string
    {
        return match ($purpose) {
            'register' => Redis::get($this->registerIndexKey($mobile)),
            'login' => $userId
                ? Redis::get($this->loginUserIndexKey($userId))
                : Redis::get($this->loginMobileIndexKey($mobile)),
            'reset' => $userId ? Redis::get($this->resetUserIndexKey($userId)) : null,
            default => null,
        };
    }

    protected function deletePurposeKeys(string $purpose, string $mobile, ?string $token = null, ?int $userId = null): void
    {
        if (!$token) {
            $token = $this->resolveToken($purpose, $mobile, $userId);
        }

        if ($token) {
            Redis::del($this->dataKey($token));
            Redis::del($this->tokenIndexKey($token));
        }

        Redis::del($this->registerIndexKey($mobile));
        Redis::del($this->loginMobileIndexKey($mobile));

        if ($userId) {
            Redis::del($this->loginUserIndexKey($userId));
            Redis::del($this->resetUserIndexKey($userId));
        }
    }

    protected function dataKey(string $token): string
    {
        return "otp:data:{$token}";
    }

    protected function tokenIndexKey(string $token): string
    {
        return "otp:token:{$token}";
    }

    protected function registerIndexKey(string $mobile): string
    {
        return "otp:register:{$mobile}";
    }

    protected function loginMobileIndexKey(string $mobile): string
    {
        return "otp:login:mobile:{$mobile}";
    }

    protected function loginUserIndexKey(int $userId): string
    {
        return "otp:login:user:{$userId}";
    }

    protected function resetUserIndexKey(int $userId): string
    {
        return "otp:reset:user:{$userId}";
    }

    protected function cooldownKey(string $purpose, string $mobile, ?int $userId = null): string
    {
        $identifier = in_array($purpose, ['reset', 'login'], true) && $userId
            ? "user:{$userId}"
            : "mobile:{$mobile}";

        return "otp:cooldown:{$purpose}:{$identifier}";
    }
}
