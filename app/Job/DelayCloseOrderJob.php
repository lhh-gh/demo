<?php

declare(strict_types=1);

namespace App\Job;

use Hyperf\AsyncQueue\Job;

class DelayCloseOrderJob extends Job
{
    public function __construct(
        public string $orderNo
    ) {
    }

    public function handle(): void
    {
        var_dump('=== DelayCloseOrderJob handle ===');
        var_dump([
            'order_no' => $this->orderNo,
            'message' => '延迟关闭订单执行成功',
            'execute_time' => date('Y-m-d H:i:s'),
        ]);
    }
}