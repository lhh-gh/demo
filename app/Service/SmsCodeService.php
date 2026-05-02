<?php

declare(strict_types=1);

namespace App\Service;

class SmsCodeService
{
    public function __construct(
        protected RedisService $redisService
    ) {
    }

    public function sendCode(string $mobile): array
    {
        $sendLockKey = "sms:lock:{$mobile}";
        $codeKey = "sms:code:{$mobile}";

        if ($this->redisService->exists($sendLockKey)) {
            return [
                'code' => 429,
                'message' => '发送过于频繁，请稍后再试',
                'data' => null,
            ];
        }

        $code = (string) random_int(100000, 999999);

        $this->redisService->set($codeKey, $code, 300);
        $this->redisService->set($sendLockKey, 1, 60);

        return [
            'code' => 0,
            'message' => '验证码发送成功',
            'data' => [
                'mobile' => $mobile,
                'code' => $code,
                'expire' => 300,
            ],
        ];
    }

    public function verifyCode(string $mobile, string $code): array
    {
        $codeKey = "sms:code:{$mobile}";
        $cachedCode = $this->redisService->get($codeKey);

        if (! $cachedCode) {
            return [
                'code' => 400,
                'message' => '验证码已过期',
                'data' => null,
            ];
        }

        if ($cachedCode !== $code) {
            return [
                'code' => 400,
                'message' => '验证码错误',
                'data' => null,
            ];
        }

        $this->redisService->delete($codeKey);

        return [
            'code' => 0,
            'message' => '验证码校验成功',
            'data' => null,
        ];
    }
}