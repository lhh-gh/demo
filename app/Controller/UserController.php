<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UserServiceInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: "user")]
class UserController
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {
    }

    #[GetMapping("info/{id}")]
    public function info(int $id): array
    {
        return [
            'method' => 'constructor inject with interface',
            'data' => $this->userService->getUserInfo($id),
        ];
    }
}