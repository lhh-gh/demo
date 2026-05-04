<?php

declare(strict_types=1);

namespace App\Job;

use App\Model\DemoDelayOrder;
use Hyperf\AsyncQueue\Job;

class DelayCloseOrderBizJob extends Job
{
    public function __construct(
        public string $orderNo
    ) {
    }

    public function handle(): void
    {
        var_dump('=== DelayCloseOrderBizJob handle ===');
        var_dump([
            'order_no' => $this->orderNo,
            'execute_time' => date('Y-m-d H:i:s'),
        ]);

        $order = DemoDelayOrder::query()
            ->where('order_no', $this->orderNo)
            ->first();

        if (! $order) {
            var_dump('订单不存在，跳过关闭');
            return;
        }

        // 关键点：
        // 延迟任务执行时，必须再次检查订单状态
        // 防止用户已经支付成功，却被错误关闭
        if ((int) $order->status !== 1) {
            var_dump('订单状态不是待支付，跳过关闭');
            var_dump([
                'order_no' => $order->order_no,
                'status' => $order->status,
            ]);
            return;
        }

        $order->status = 3;
        $order->save();

        var_dump('订单超时未支付，已自动关闭');
        var_dump([
            'order_no' => $order->order_no,
            'status' => $order->status,
        ]);
    }
}