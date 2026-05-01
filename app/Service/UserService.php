<?php

declare(strict_types=1);

namespace App\Service;

/**
 *  定义实现类
 */
class UserService implements UserServiceInterface
{
    public function getUserInfo(int $id): array
    {
        return [
            'id' => $id,
            'name' => '李四',
            'email' => 'lisi@example.com',
        ];
    }
}