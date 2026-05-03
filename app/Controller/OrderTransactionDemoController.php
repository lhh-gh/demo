<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\OrderTransactionDemoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;

#[Controller(prefix: 'order-transaction')]
class OrderTransactionDemoController
{
    public function __construct(
        protected OrderTransactionDemoService $service
    ) {
    }

    #[PostMapping('create')]
    public function create(): array
    {
        return $this->service->createOrder();
    }
}