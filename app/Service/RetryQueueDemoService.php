<?php

declare(strict_types=1);

namespace App\Service;

use App\Job\RetryOrderNotifyJob;
use Hyperf\AsyncQueue\Driver\DriverFactory;

class RetryQueueDemoService
{
    public function __construct(
        protected DriverFactory $driverFactory
    ) {
    }

    public function pushRetryJob(): array
    {
        $orderNo = 'RT' . date('YmdHis') . mt_rand(1000, 9999);

        $driver = $this->driverFactory->get('default');
        $driver->push(new RetryOrderNotifyJob($orderNo));

        return [
            'code' => 0,
            'message' => '失败重试任务已投递',
            'data' => [
                'order_no' => $orderNo,
            ],
        ];
    }
}