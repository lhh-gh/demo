<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\TransactionErrorCode;
use App\Exception\BusinessException;
use App\Exception\TransactionBusinessException;
use App\Model\DemoOrder;
use App\Model\DemoOrderItem;
use App\Model\Inventory;
use App\Model\OrderLog;
use Hyperf\DbConnection\Db;

class OrderSubmitService
{
    public function __construct(
        protected RedisService $redisService
    )
    {
    }

    /**
     * 幂等 + 事务 + 条件扣库存
     */
    public function submit(int $userId, int $skuId, int $quantity, string $requestId): array
    {
        if ($quantity <= 0) {
            throw new TransactionBusinessException(TransactionErrorCode::ORDER_QUANTITY_INVALID);
        }

        // 1. 幂等 key，防止重复提交
        $idempotentKey = "order:submit:{$userId}:{$requestId}";
        $locked = $this->redisService->getClient()->set($idempotentKey, 1, ['nx', 'ex' => 10]);

        if (!$locked) {
            throw new TransactionBusinessException(TransactionErrorCode::ORDER_REPEAT_SUBMIT);
        }
        $success = false;
        try {
            $result = Db::transaction(function () use ($userId, $skuId, $quantity) {
                $inventory = Inventory::query()
                    ->where('sku_id', $skuId)
                    ->first();

                if (!$inventory) {
                    throw new TransactionBusinessException(TransactionErrorCode::INVENTORY_NOT_FOUND);
                }

                $price = 199.00;
                $amount = bcmul((string)$price, (string)$quantity, 2);

                // 2. 创建订单主表
                $order = DemoOrder::query()->create([
                    'order_no' => 'ORD' . date('YmdHis') . mt_rand(1000, 9999),
                    'user_id' => $userId,
                    'total_amount' => $amount,
                    'status' => 1,
                    'remark' => '幂等 + 事务 + 条件扣库存 Demo',
                ]);

                // 3. 创建订单明细
                DemoOrderItem::query()->create([
                    'order_id' => $order->id,
                    'sku_id' => $skuId,
                    'product_name' => '测试商品A',
                    'price' => $price,
                    'quantity' => $quantity,
                    'amount' => $amount,
                ]);

                // 4. 条件扣库存
                $affected = Inventory::query()
                    ->where('sku_id', $skuId)
                    ->where('stock', '>=', $quantity)
                    ->decrement('stock', $quantity);

                if ($affected === 0) {
                    throw new TransactionBusinessException(TransactionErrorCode::STOCK_NOT_ENOUGH);
                }

                // 5. 写日志
                OrderLog::query()->create([
                    'order_id' => $order->id,
                    'content' => '订单创建成功，库存扣减成功，幂等校验通过',
                ]);

                return [
                    'code' => 0,
                    'message' => '下单成功',
                    'data' => [
                        'order_id' => $order->id,
                        'order_no' => $order->order_no,
                    ],
                ];
            });
            $success = true;

            return $result;
        } finally {
            // 说明：
            // 如果你想防“短时间重复点击”，这里可以不删，让它自然过期
            // 如果你想允许失败后立即重试，可以按业务决定是否删除

            // 成功不删：防重复提交
            // 失败删除：允许用户重新提交
            if (!$success) {
                $this->redisService->delete($idempotentKey);
            }
        }
    }
}