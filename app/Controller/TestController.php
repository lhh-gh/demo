<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\ErrorCode;
use App\Service\TestService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'test')]
class TestController
{
    public function __construct(
        protected TestService $userService
    ) {
    }

    #[GetMapping('info')]
    public function info(): array
    {
        $user = $this->userService->getUserInfo(2);

        return [
            'code' => ErrorCode::SUCCESS,
            'message' => 'success',
            'data' => $user,
        ];
    }
}