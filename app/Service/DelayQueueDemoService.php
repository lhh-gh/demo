<?php

declare(strict_types=1);

namespace App\Service;

use App\Job\DelayCloseOrderJob;
use Hyperf\AsyncQueue\Driver\DriverFactory;

class DelayQueueDemoService
{
    public function __construct(
        protected DriverFactory $driverFactory
    ) {
    }

    public function pushDelayJob(): array
    {
        $orderNo = 'DL' . date('YmdHis') . mt_rand(1000, 9999);

        $driver = $this->driverFactory->get('default');

        // 延迟 10 秒执行：注意是 push 的第二个参数
        $driver->push(new DelayCloseOrderJob($orderNo), 10);

        return [
            'code' => 0,
            'message' => '延迟任务已投递，10 秒后执行',
            'data' => [
                'order_no' => $orderNo,
                'delay_seconds' => 10,
                'push_time' => date('Y-m-d H:i:s'),
            ],
        ];
    }
}