<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TransactionBusinessDemoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\Swagger\Annotation as SA;

#[SA\HyperfServer('http')]
#[Controller]
class TransactionBusinessDemoController
{
    public function __construct(
        protected TransactionBusinessDemoService $service
    ) {
    }

    #[SA\Post(
        path: '/transaction-business/submit',
        summary: '事务下单并扣库存',
        tags: ['事务下单']
    )]
    #[SA\RequestBody(
        description: '下单请求参数',
        content: [
            new SA\MediaType(
                mediaType: 'application/json',
                schema: new SA\Schema(
                    required: ['sku_id', 'quantity'],
                    properties: [
                        new SA\Property(
                            property: 'sku_id',
                            type: 'integer',
                            description: '商品 SKU ID',
                            example: 1001
                        ),
                        new SA\Property(
                            property: 'quantity',
                            type: 'integer',
                            description: '购买数量',
                            example: 1
                        ),
                    ]
                ),
            ),
        ],
    )]
    #[SA\Response(
        response: 200,
        description: '下单成功',
        content: new SA\JsonContent(
            properties: [
                new SA\Property(property: 'code', type: 'integer', example: 0),
                new SA\Property(property: 'message', type: 'string', example: '下单成功'),
                new SA\Property(
                    property: 'data',
                    properties: [
                        new SA\Property(
                            property: 'order',
                            properties: [
                                new SA\Property(property: 'id', type: 'integer', example: 1),
                                new SA\Property(property: 'order_no', type: 'string', example: 'BIZ202605031200009999'),
                                new SA\Property(property: 'sku_id', type: 'integer', example: 1001),
                                new SA\Property(property: 'quantity', type: 'integer', example: 1),
                                new SA\Property(property: 'status', type: 'integer', example: 1),
                                new SA\Property(
                                    property: 'remark',
                                    type: 'string',
                                    example: '事务 + BusinessException 企业版 Demo'
                                ),
                            ],
                            type: 'object'
                        ),
                        new SA\Property(
                            property: 'inventory',
                            properties: [
                                new SA\Property(property: 'sku_id', type: 'integer', example: 1001),
                                new SA\Property(property: 'left_stock', type: 'integer', example: 9),
                            ],
                            type: 'object'
                        ),
                    ],
                    type: 'object'
                ),
            ],
            type: 'object'
        )
    )]
    #[SA\Response(
        response: 500,
        description: '业务异常',
        content: new SA\JsonContent(
            properties: [
                new SA\Property(property: 'code', type: 'integer', example: 20002),
                new SA\Property(property: 'message', type: 'string', example: '库存不足'),
                new SA\Property(property: 'data', type: 'null', example: null),
            ],
            type: 'object'
        )
    )]
    public function submit(): array
    {
        return $this->service->createOrderAndDeductStock(1001, 1);
    }
}