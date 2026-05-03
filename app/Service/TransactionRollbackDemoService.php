<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\DemoTransactionOrder;
use App\Model\Inventory;
use Hyperf\DbConnection\Db;
use RuntimeException;

/**
 * 事务自动回滚 Demo
 */
class TransactionRollbackDemoService
{
    /**
     * 中途报错自动回滚
     *
     * 执行流程：
     * 1. 先创建订单
     * 2. 再查询库存
     * 3. 模拟扣库存时报错
     * 4. 整个事务自动回滚
     */
    public function createOrderAndRollback(int $skuId, int $quantity): array
    {
        Db::transaction(function () use ($skuId, $quantity) {
            // 1. 创建订单
            DemoTransactionOrder::query()->create([
                'order_no' => 'RB' . date('YmdHis') . mt_rand(1000, 9999),
                'sku_id' => $skuId,
                'quantity' => $quantity,
                'status' => 1,
                'remark' => '事务中途报错自动回滚 Demo',
            ]);

            // 2. 查询库存
            $inventory = Inventory::query()
                ->where('sku_id', $skuId)
                ->lockForUpdate()
                ->first();

            if (! $inventory) {
                throw new RuntimeException('库存记录不存在');
            }

            // 3. 模拟扣库存时报错
            throw new RuntimeException('模拟异常：扣库存失败，触发事务自动回滚');

            // 4. 这段代码不会执行到
            $inventory->stock -= $quantity;
            $inventory->save();
        });

        return [
            'code' => 0,
            'message' => '这行一般不会执行到',
            'data' => null,
        ];
    }
}