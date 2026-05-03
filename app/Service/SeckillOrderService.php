<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\TransactionErrorCode;
use App\Exception\BusinessException;
use App\Model\DemoOrder;
use App\Model\DemoOrderItem;
use App\Model\OrderLog;
use Hyperf\DbConnection\Db;

class SeckillOrderService
{
    public function __construct(
        protected RedisService $redisService
    )
    {
    }

    /**
     * 秒杀防超卖基础版
     *
     * 思路：
     * 1. Redis 先预扣库存
     * 2. 抢到库存资格后再落库
     * 3. 数据库事务写订单
     */
    public function seckill(int $userId, int $skuId, int $quantity, string $requestId): array
    {
        $idempotentKey = "seckill:submit:{$userId}:{$requestId}";
        $stockKey = "seckill:stock:{$skuId}";

        // 1. 幂等校验
        $ok = $this->redisService->getClient()->set($idempotentKey, 1, ['nx', 'ex' => 10]);
        if (!$ok) {
            throw new BusinessException(TransactionErrorCode::ORDER_REPEAT_SUBMIT);
        }

        // 2. Redis 原子预扣库存
//        $lua = <<<'LUA'
//  local stock = tonumber(redis.call('get', KEYS[1]))
//  local num = tonumber(ARGV[1])
//
//  if not stock then
//      return -1
//  end
//
//  if stock < num then
//      return 0
//  end
//
//  redis.call('decrby', KEYS[1], num)
//  return 1
//  LUA;
//        $lua = 'local stock = tonumber(redis.call("get", KEYS[1]))
//  local num = tonumber(ARGV[1])
//
//  if not stock then
//      return -1
//  end
//
//  if stock < num then
//      return 0
//  end
//
//  redis.call("decrby", KEYS[1], num)
//  return 1';

        // 2. Redis 原子预扣库存
        $lua = <<<'LUA'
  local stock = tonumber(redis.call('get', KEYS[1]))
  local num = tonumber(ARGV[1])

  if not stock then
      return -1
  end

  if stock < num then
      return 0
  end

  redis.call('decrby', KEYS[1], num)
  return 1
  LUA;
        $result = $this->redisService->getClient()->eval($lua, [$stockKey, $quantity], 1);

        if ((int)$result === -1) {
            throw new BusinessException(TransactionErrorCode::INVENTORY_NOT_FOUND);
        }

        if ((int)$result === 0) {
            throw new BusinessException(TransactionErrorCode::STOCK_NOT_ENOUGH);
        }

        try {
            // 3. 抢到资格后再写数据库
            return Db::transaction(function () use ($userId, $skuId, $quantity) {
                $price = 99.00;
                $amount = bcmul((string)$price, (string)$quantity, 2);

                $order = DemoOrder::query()->create([
                    'order_no' => 'SK' . date('YmdHis') . mt_rand(1000, 9999),
                    'user_id' => $userId,
                    'total_amount' => $amount,
                    'status' => 1,
                    'remark' => '秒杀下单 Demo',
                ]);

                DemoOrderItem::query()->create([
                    'order_id' => $order->id,
                    'sku_id' => $skuId,
                    'product_name' => '秒杀商品',
                    'price' => $price,
                    'quantity' => $quantity,
                    'amount' => $amount,
                ]);

                OrderLog::query()->create([
                    'order_id' => $order->id,
                    'content' => '秒杀订单创建成功',
                ]);

                return [
                    'code' => 0,
                    'message' => '秒杀成功',
                    'data' => [
                        'order_id' => $order->id,
                        'order_no' => $order->order_no,
                    ],
                ];
            });
        } catch (\Throwable $throwable) {
            // 4. 数据库失败，Redis 库存补偿回滚
            $this->redisService->getClient()->incrBy($stockKey, $quantity);
            throw $throwable;
        }
    }
}