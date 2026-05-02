<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;

class TestService
{
    public function getUserInfo(int $id): array
    {
        if ($id !== 1) {
            throw new BusinessException(ErrorCode::USER_NOT_FOUND);
        }

        return [
            'id' => 1,
            'name' => '张三',
            'email' => 'zhangsan@example.com',
        ];
    }
}