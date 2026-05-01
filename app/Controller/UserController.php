<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

//
#[Controller(prefix: "user")]
class UserController
{    
    // 在构造函数声明参数的类型，Hyperf 会自动注入对应的对象或值
    public function __construct(
        protected UserService $userService
    )
    {
    }

    #[GetMapping("info/{id}")]
    public function info(int $id): array
    {
        return [
            'method' => 'constructor inject',
            'data' => $this->userService->getUserInfo($id),
        ];
    }
}