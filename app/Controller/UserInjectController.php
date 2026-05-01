<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UserServiceInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'user-inject')]
class UserInjectController
{
    #[Inject]
    protected UserServiceInterface $userService;

    #[GetMapping('info/{id:\d+}')]
    public function info(int $id): array
    {
        return [
            'inject_type' => 'attribute_inject',
            'result' => $this->userService->getUserInfo($id),
        ];
    }
}