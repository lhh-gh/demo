<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TransactionBusinessDemoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use OpenApi\Attributes as OA;
use Hyperf\Swagger\Annotation as HA;

#[HA\HyperfServer('http')]
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
        operationId: 'transactionBusinessSubmit',
        summary: '事务下单并扣库存',
        description: '创建订单、扣减库存、成功后提交事务；库存不足时抛出业务异常',
        tags: ['Transaction'],
        requestBody: new OA\RequestBody(
            required: true,
            description: '下单请求参数',
            content: new OA\JsonContent(
                required: ['sku_id', 'quantity'],
                properties: [
                    new OA\Property(
                        property: 'sku_id',
                        description: '商品 SKU ID',
                        type: 'integer',
                        example: 1001
                    ),
                    new OA\Property(
                        property: 'quantity',
                        description: '购买数量',
                        type: 'integer',
                        example: 1
                    ),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: '下单成功',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'code', type: 'integer', example: 0),
                        new OA\Property(property: 'message', type: 'string', example: '下单成功'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'order',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 1),
                                        new OA\Property(property: 'order_no', type: 'string', example: 'BIZ202605031230309999'),
                                        new OA\Property(property: 'sku_id', type: 'integer', example: 1001),
                                        new OA\Property(property: 'quantity', type: 'integer', example: 1),
                                        new OA\Property(property: 'status', type: 'integer', example: 1),
                                        new OA\Property(property: 'remark', type: 'string', example: '事务 + BusinessException 企业版 Demo'),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'inventory',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'sku_id', type: 'integer', example: 1001),
                                        new OA\Property(property: 'left_stock', type: 'integer', example: 9),
                                    ]
                                ),
                            ]
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 500,
                description: '业务异常或系统异常',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'code', type: 'integer', example: 20002),
                        new OA\Property(property: 'message', type: 'string', example: '库存不足'),
                        new OA\Property(property: 'data', example: null),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[PostMapping('submit')]
    public function submit(): array
    {
        $skuId = 1001;
        $quantity = 1;

        return $this->service->createOrderAndDeductStock($skuId, $quantity);
    }
}