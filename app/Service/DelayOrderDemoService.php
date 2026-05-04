<?php

declare(strict_types=1);

namespace App\Service;

use App\Job\DelayCloseOrderBizJob;
use App\Model\DemoDelayOrder;
use Hyperf\AsyncQueue\Driver\DriverFactory;

class DelayOrderDemoService
{
    public function __construct(
        protected DriverFactory $driverFactory
    ) {
    }

    /**
     * 创建待支付订单，并投递延迟关闭任务
     */
    public function createOrder(): array
    {
        $orderNo = 'OD' . date('YmdHis') . mt_rand(1000, 9999);

        $order = DemoDelayOrder::query()->create([
            'order_no' => $orderNo,
            'user_id' => 1001,
            'amount' => 99.90,
            'status' => 1, // 1=待支付
        ]);

        $driver = $this->driverFactory->get('default');

        // Demo 为了方便测试，延迟 15 秒关闭
        // 企业里通常是 15 分钟 / 30 分钟
        $driver->push(new DelayCloseOrderBizJob($orderNo), 15);

        return [
            'code' => 0,
            'message' => '订单创建成功，已投递延迟关闭任务',
            'data' => [
                'id' => $order->id,
                'order_no' => $order->order_no,
                'status' => $order->status,
                'close_after_seconds' => 15,
            ],
        ];
    }

    /**
     * 模拟支付成功
     */
    public function payOrder(string $orderNo): array
    {
        $order = DemoDelayOrder::query()
            ->where('order_no', $orderNo)
            ->first();

        if (! $order) {
            return [
                'code' => 404,
                'message' => '订单不存在',
                'data' => null,
            ];
        }

        if ((int) $order->status !== 1) {
            return [
                'code' => 400,
                'message' => '当前订单不是待支付状态，不能支付',
                'data' => [
                    'order_no' => $order->order_no,
                    'status' => $order->status,
                ],
            ];
        }

        $order->status = 2; // 2=已支付
        $order->save();

        return [
            'code' => 0,
            'message' => '支付成功',
            'data' => [
                'order_no' => $order->order_no,
                'status' => $order->status,
            ],
        ];
    }

    /**
     * 查询订单详情
     */
    public function detail(string $orderNo): array
    {
        $order = DemoDelayOrder::query()
            ->where('order_no', $orderNo)
            ->first();

        if (! $order) {
            return [
                'code' => 404,
                'message' => '订单不存在',
                'data' => null,
            ];
        }

        return [
            'code' => 0,
            'message' => 'success',
            'data' => $order->toArray(),
        ];
    }
}