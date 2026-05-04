<?php

declare(strict_types=1);

namespace App\Service;

use App\Job\IdempotentCouponJob;
use Hyperf\AsyncQueue\Driver\DriverFactory;

class IdempotentQueueDemoService
{
    public function __construct(
        protected DriverFactory $driverFactory
    ) {
    }

    public function pushIdempotentJob(): array
    {
        $userId = 1001;

        // 故意固定 request_id，方便你测试重复消费
        $requestId = 'req-demo-001';

        $driver = $this->driverFactory->get('default');

        // 连续 push 两次同一个任务
        $driver->push(new IdempotentCouponJob($userId, $requestId));
        $driver->push(new IdempotentCouponJob($userId, $requestId));

        return [
            'code' => 0,
            'message' => '幂等消费测试任务已投递两次',
            'data' => [
                'user_id' => $userId,
                'request_id' => $requestId,
            ],
        ];
    }
}