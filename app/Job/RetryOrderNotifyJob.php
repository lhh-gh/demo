<?php

declare(strict_types=1);

namespace App\Job;

use Hyperf\AsyncQueue\Job;

class RetryOrderNotifyJob extends Job
{
    /**
     * 最大重试次数
     */
    protected int $maxAttempts = 3;

    public function __construct(
        public string $orderNo
    ) {
    }

    /**
     * 执行任务
     */
    public function handle(): void
    {
        var_dump('=== RetryOrderNotifyJob handle ===');
        var_dump([
            'order_no' => $this->orderNo,
            'attempts' => $this->attempts(),
        ]);

        // 模拟消费失败
        throw new \RuntimeException('模拟通知第三方失败');
    }
}