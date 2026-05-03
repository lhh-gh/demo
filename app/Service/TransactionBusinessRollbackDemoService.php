<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\TransactionErrorCode;
use App\Exception\BusinessException;
use App\Exception\TransactionBusinessException;
use App\Model\DemoTransactionOrder;
use App\Model\Inventory;
use Hyperf\DbConnection\Db;

/**
 * TransactionBusinessException 回滚 Demo
 */
class TransactionBusinessRollbackDemoService
{
    /**
     * 执行流程：
     * 1. 开事务
     * 2. 创建订单
     * 3. 查询库存
     * 4. 库存不足时抛业务异常
     * 5. 自动回滚事务
     */
    public function createOrderAndRollback(int $skuId, int $quantity): array
    {
        return Db::transaction(function () use ($skuId, $quantity) {
            // 1. 创建订单
            DemoTransactionOrder::query()->create([
                'order_no' => 'BRB' . date('YmdHis') . mt_rand(1000, 9999),
                'sku_id' => $skuId,
                'quantity' => $quantity,
                'status' => 1,
                'remark' => 'BusinessException 回滚 Demo',
            ]);

            // 2. 查询库存并加锁
            $inventory = Inventory::query()
                ->where('sku_id', $skuId)
                ->lockForUpdate()
                ->first();

            if (! $inventory) {
                throw new BusinessException(TransactionErrorCode::INVENTORY_NOT_FOUND);
            }

            // 3. 故意让它回滚
            if ($inventory->stock < $quantity) {
                throw new TransactionBusinessException(TransactionErrorCode::STOCK_NOT_ENOUGH);
            }

            // 你如果想强制演示回滚，也可以直接抛
            throw new TransactionBusinessException(TransactionErrorCode::STOCK_NOT_ENOUGH);

            // 这段不会执行
            $inventory->stock -= $quantity;
            $inventory->save();

            return [
                'code' => 0,
                'message' => 'success',
                'data' => null,
            ];
        });
    }
}