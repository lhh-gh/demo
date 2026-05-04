<?php

declare(strict_types=1);

namespace App\Service;

use App\Job\SendOrderSmsJob;
use Hyperf\AsyncQueue\Driver\DriverFactory;

class QueueDemoService
{
    public function __construct(
        protected DriverFactory $driverFactory
    ) {
    }

    /**
     * 模拟下单成功后，异步投递短信任务
     */
    public function createOrderAndPushJob(): array
    {
        $orderNo = 'ORD' . date('YmdHis') . mt_rand(1000, 9999);
        $mobile = '13800138000';

        // 获取默认队列驱动
        $driver = $this->driverFactory->get('default');

        // 投递任务到队列
        $driver->push(new SendOrderSmsJob($mobile, $orderNo));

        return [
            'code' => 0,
            'message' => '下单成功，短信任务已投递到队列',
            'data' => [
                'order_no' => $orderNo,
                'mobile' => $mobile,
            ],
        ];
    }
}