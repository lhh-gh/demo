<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\RedisLockService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;

#[Controller(prefix: 'order')]
class OrderController
{
    public function __construct(
        protected RedisLockService $lockService
    ) {
    }

    #[PostMapping('submit')]
    public function submit(): array
    {
        $userId = 1001;
        $lockKey = "lock:order:submit:{$userId}";

        $result = $this->lockService->executeWithLock($lockKey, function () use ($userId) {
            return [
                'code' => 0,
                'message' => '下单成功',
                'data' => [
                    'user_id' => $userId,
                    'order_no' => 'ORD' . date('YmdHis'),
                ],
            ];
        }, 5);

        return is_array($result) ? $result : [
            'code' => 0,
            'message' => 'success',
            'data' => $result,
        ];
    }
}