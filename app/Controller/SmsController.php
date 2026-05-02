<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SmsCodeService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'sms')]
class SmsController
{
    public function __construct(
        protected SmsCodeService $smsCodeService
    ) {
    }

    #[GetMapping('send')]
    public function send(): array
    {
        $mobile = '13800138000';

        return $this->smsCodeService->sendCode($mobile);
    }

    #[GetMapping('verify')]
    public function verify(): array
    {
        $mobile = '13800138000';
        $code = '123456';

        return $this->smsCodeService->verifyCode($mobile, $code);
    }
}