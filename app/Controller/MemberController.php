<?php

declare(strict_types=1);

namespace App\Controller;

use App\Middleware\CheckTokenMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;

#[Controller(prefix: 'member')]
#[Middleware(CheckTokenMiddleware::class)]
class MemberController
{
    #[GetMapping('profile')]
    public function profile(): array
    {
        return [
            'code' => 0,
            'message' => 'success',
            'data' => [
                'id' => 1,
                'nickname' => 'zhangsan',
            ],
        ];
    }

    #[GetMapping('account')]
    public function account(): array
    {
        return [
            'code' => 0,
            'message' => 'success',
            'data' => [
                'mobile' => '13800138000',
                'email' => 'zhangsan@example.com',
            ],
        ];
    }
}