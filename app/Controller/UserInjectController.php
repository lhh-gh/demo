<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UserServiceInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: "user-inject")]
class UserInjectController
{
    #[Inject]
    protected UserServiceInterface $userService;

    #[GetMapping("info/{id}")]
    public function info(int $id): array
    {
        return [
            'method' => '#[Inject] with interface',
            'data' => $this->userService->getUserInfo($id),
        ];
    }
}