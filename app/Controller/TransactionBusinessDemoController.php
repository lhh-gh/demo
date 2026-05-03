<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TransactionBusinessDemoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;

#[Controller(prefix: 'transaction')]
class TransactionBusinessDemoController
{
    public function __construct(
        protected TransactionBusinessDemoService $service
    )
    {
    }

    /**
     * 创建订单并扣库存
     */
    #[PostMapping('submit')]
    public function submit(): array
    {
        return $this->service->createOrderAndDeductStock(1001, 1);
    }
}