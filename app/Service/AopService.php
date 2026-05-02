<?php
declare(strict_types=1);
namespace App\Service;

// 业务类
class AopService
{
    public function getUserInfo(int $id): array
    {
        return [
            'id' => $id,
            'name' => '张三',
        ];
    }
}