<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: "user-inject")]
class UserInjectController
{
    /**
     * 通过 #[Inject] 注解注入
     */
    #[Inject]
    protected UserService $userService;

    #[GetMapping("info/{id}")]
    public function info(int $id): array
    {
        return [
            'method' => '#[Inject] inject',
            'data' => $this->userService->getUserInfo($id),
        ];
    }
}