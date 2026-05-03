<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\TransactionErrorCode;
use App\Exception\TransactionBusinessException;
use App\Model\DemoTransactionOrder;
use App\Model\Inventory;
use Hyperf\DbConnection\Db;
use Throwable;

/**
 * 企业版事务 + BusinessException Demo
 *
 * 场景：
 * - 创建订单
 * - 扣库存
 * - 最后 commit
 */
class TransactionBusinessDemoService
{
    /**
     * 创建订单并扣库存
     */
    public function createOrderAndDeductStock(int $skuId, int $quantity): array
    {
        // 1. 参数校验
        if ($quantity <= 0) {
            throw new TransactionBusinessException(TransactionErrorCode::ORDER_QUANTITY_INVALID);
        }

        // 2. 开启事务
        Db::beginTransaction();

        try {
            // 3. 查询库存，并加行锁，防止并发超卖
            $inventory = Inventory::query()
                ->where('sku_id', $skuId)
                ->lockForUpdate()
                ->first();

            if (! $inventory) {
                throw new TransactionBusinessException(TransactionErrorCode::INVENTORY_NOT_FOUND);
            }

            // 4. 判断库存是否充足
            if ($inventory->stock < $quantity) {
                throw new TransactionBusinessException(TransactionErrorCode::STOCK_NOT_ENOUGH);
            }

            // 5. 创建订单
            $order = DemoTransactionOrder::query()->create([
                'order_no' => 'BIZ' . date('YmdHis') . mt_rand(1000, 9999),
                'sku_id' => $skuId,
                'quantity' => $quantity,
                'status' => 1,
                'remark' => '事务 + BusinessException 企业版 Demo',
            ]);

            if (! $order) {
                throw new TransactionBusinessException(TransactionErrorCode::ORDER_CREATE_FAILED);
            }

            // 6. 扣减库存
            $inventory->stock -= $quantity;
            $inventory->save();

            // 7. 最后提交事务
            Db::commit();

            return [
                'code' => TransactionErrorCode::SUCCESS,
                'message' => '下单成功',
                'data' => [
                    'order' => $order->toArray(),
                    'inventory' => [
                        'sku_id' => $skuId,
                        'left_stock' => $inventory->stock,
                    ],
                ],
            ];
        } catch (TransactionBusinessException $exception) {
            // 业务异常：回滚后继续抛出
            Db::rollBack();
            throw $exception;
        } catch (Throwable $throwable) {
            // 系统异常：回滚后包装成业务异常抛出
            Db::rollBack();
            throw new TransactionBusinessException(
                TransactionErrorCode::ORDER_CREATE_FAILED,
                '事务执行失败：' . $throwable->getMessage(),
                $throwable
            );
        }
    }
}