<?php

declare(strict_types=1);

namespace App\Service;

class UserService
{
    public function getUserInfo(int $id): array
    {
        return [
            'id' => $id,
            'name' => '张三',
            'email' => 'zhangsan@example.com',
        ];
    }
}