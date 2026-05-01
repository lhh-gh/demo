<?php

declare(strict_types=1);

namespace App\Service;

/**
 *  定义接口
 */
interface UserServiceInterface
{
    public function getUserInfo(int $id): array;
}