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

class OrderTransactionDemoService
{
    /**
     * 多表事务 Demo
     *
     * 涉及：
     * - orders
     * - order_items
     * - inventory
     * - order_logs
     */
    public function createOrder(): array
    {
        return Db::transaction(function () {
            $skuId = 1001;
            $quantity = 2;
            $price = 199.00;
            $amount = bcmul((string) $price, (string) $quantity, 2);

            // 1. 查询库存并加锁
            $inventory = Inventory::query()
                ->where('sku_id', $skuId)
                ->lockForUpdate()
                ->first();

            if (! $inventory) {
                throw new BusinessException(TransactionErrorCode::INVENTORY_NOT_FOUND);
            }

            if ($inventory->stock < $quantity) {
                throw new TransactionBusinessException(TransactionErrorCode::STOCK_NOT_ENOUGH);
            }

            // 2. 创建订单主表
            $order = DemoOrder::query()->create([
                'order_no' => 'ORD' . date('YmdHis') . mt_rand(1000, 9999),
                'user_id' => 1,
                'total_amount' => $amount,
                'status' => 1,
                'remark' => '事务多表更新 Demo',
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

            // 4. 扣减库存
            $inventory->stock -= $quantity;
            $inventory->save();

            // 5. 写订单日志
            OrderLog::query()->create([
                'order_id' => $order->id,
                'content' => '订单创建成功，扣减库存完成',
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