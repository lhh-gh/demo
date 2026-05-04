<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DelayOrderDemoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Contract\RequestInterface;

#[Controller(prefix: 'delay-order-demo')]
class DelayOrderDemoController
{
    public function __construct(
        protected DelayOrderDemoService $service,
        protected RequestInterface $request
    ) {
    }

    /**
     * 创建订单
     */
    #[GetMapping('create')]
    public function create(): array
    {
        return $this->service->createOrder();
    }

    /**
     * 模拟支付
     * /delay-order-demo/pay?order_no=xxxx
     */
    #[GetMapping('pay')]
    public function pay(): array
    {
        $orderNo = (string) $this->request->input('order_no', '');

        return $this->service->payOrder($orderNo);
    }

    /**
     * 查询详情
     * /delay-order-demo/detail?order_no=xxxx
     */
    #[GetMapping('detail')]
    public function detail(): array
    {
        $orderNo = (string) $this->request->input('order_no', '');

        return $this->service->detail($orderNo);
    }
}