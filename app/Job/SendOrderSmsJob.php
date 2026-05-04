<?php

declare(strict_types=1);

namespace App\Job;

use Hyperf\AsyncQueue\Job;

class SendOrderSmsJob extends Job
{
    /**
     * 短信接收手机号
     */
    public string $mobile;

    /**
     * 订单号
     */
    public string $orderNo;

    /**
     * 重试次数
     */
    protected int $maxAttempts = 2;

    public function __construct(string $mobile, string $orderNo)
    {
        $this->mobile = $mobile;
        $this->orderNo = $orderNo;
    }

    /**
     * 队列消费时真正执行的方法
     */
    public function handle(): void
    {
        // 这里模拟发短信
        var_dump('=== SendOrderSmsJob handle ===');
        var_dump([
            'mobile' => $this->mobile,
            'order_no' => $this->orderNo,
            'message' => '短信发送成功（模拟）',
        ]);
    }
}