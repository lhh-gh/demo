<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\OrderSubmitService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;

#[Controller(prefix: 'orders')]
class OrderSubmitController
{
    public function __construct(
        protected OrderSubmitService $service
    ) {
    }

    #[PostMapping('submit')]
    public function submit(): array
    {
        $userId = 1;
        $skuId = 1001;
        $quantity = 2;
        $requestId = uniqid('req_', true);

        return $this->service->submit($userId, $skuId, $quantity, $requestId);
    }
}