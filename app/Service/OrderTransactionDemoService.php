<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\TransactionErrorCode;
use App\Exception\BusinessException;
use App\Model\DemoOrder;
use App\Model\DemoOrderItem;
use App\Model\Inventory;
use App\Model\OrderLog;
use Hyperf\DbConnection\Db;

class OrderTransactionDemoService
{
    /**
     * 企业版防超卖事务 Demo
     */
    public function createOrder(int $userId, int $skuId, int $quantity): array
    {
        if ($quantity <= 0) {
            throw new BusinessException(TransactionErrorCode::ORDER_QUANTITY_INVALID);
        }

        return Db::transaction(function () use ($userId, $skuId, $quantity) {
            $inventory = Inventory::query()
                ->where('sku_id', $skuId)
                ->first();

            if (! $inventory) {
                throw new BusinessException(TransactionErrorCode::INVENTORY_NOT_FOUND);
            }

            $price = 199.00;
            $amount = bcmul((string) $price, (string) $quantity, 2);

            // 1. 创建订单主表
            $order = DemoOrder::query()->create([
                'order_no' => 'ORD' . date('YmdHis') . mt_rand(1000, 9999),
                'user_id' => $userId,
                'total_amount' => $amount,
                'status' => 1,
                'remark' => '企业版防超卖事务 Demo',
            ]);

            // 2. 创建订单明细
            DemoOrderItem::query()->create([
                'order_id' => $order->id,
                'sku_id' => $skuId,
                'product_name' => '测试商品A',
                'price' => $price,
                'quantity' => $quantity,
                'amount' => $amount,
            ]);

            // 3. 条件扣库存：防超卖关键
            $affected = Inventory::query()
                ->where('sku_id', $skuId)
                ->where('stock', '>=', $quantity)
                ->decrement('stock', $quantity);

            if ($affected === 0) {
                throw new BusinessException(TransactionErrorCode::STOCK_NOT_ENOUGH);
            }

            // 4. 写订单日志
            OrderLog::query()->create([
                'order_id' => $order->id,
                'content' => '订单创建成功，库存扣减成功',
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
    }
}