<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TransactionBusinessDemoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Transaction', description: '事务下单演示')]
#[Controller(prefix: 'transaction-business')]
class TransactionBusinessDemoController
{
    public function __construct(
        protected TransactionBusinessDemoService $service
    ) {
    }

    #[OA\Post(
        path: '/transaction-business/submit',
        summary: '事务下单并扣库存',
        tags: ['Transaction']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['sku_id', 'quantity'],
            properties: [
                new OA\Property(property: 'sku_id', type: 'integer', example: 1001),
                new OA\Property(property: 'quantity', type: 'integer', example: 1),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: '成功返回'
    )]
    #[PostMapping('submit')]
    public function submit(): array
    {
        return $this->service->createOrderAndDeductStock(1001, 1);
    }
}