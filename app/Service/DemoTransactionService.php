<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\DemoUser;
use Hyperf\DbConnection\Db;
use Throwable;

class DemoTransactionService
{
    /**
     * 事务示例：创建两个用户
     *
     * 说明：
     * - 两条插入语句放在一个事务里
     * - 如果中间抛异常，两条都不会成功
     */
    public function createTwoUsers(): array
    {
        try {
            $result = Db::transaction(function () {
                $user1 = DemoUser::query()->create([
                    'username' => 'transaction_user_1',
                    'email' => 'transaction1@example.com',
                    'age' => 20,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $user2 = DemoUser::query()->create([
                    'username' => 'transaction_user_2',
                    'email' => 'transaction2@example.com',
                    'age' => 21,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return [
                    'user1' => $user1->toArray(),
                    'user2' => $user2->toArray(),
                ];
            });

            return [
                'code' => 0,
                'message' => '事务执行成功',
                'data' => $result,
            ];
        } catch (Throwable $e) {
            return [
                'code' => 500,
                'message' => '事务执行失败：' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * 事务回滚示例
     *
     * 说明：
     * - 第一条插入成功后，主动抛异常
     * - 整个事务会回滚
     * - 最终数据库中不会留下任何新数据
     */
    public function rollbackDemo(): array
    {
        try {
            Db::transaction(function () {
                DemoUser::query()->create([
                    'username' => 'rollback_user_1',
                    'email' => 'rollback1@example.com',
                    'age' => 25,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                // 模拟异常
                throw new \RuntimeException('手动触发异常，测试事务回滚');
            });

            return [
                'code' => 0,
                'message' => '事务执行成功',
                'data' => null,
            ];
        } catch (Throwable $e) {
            return [
                'code' => 500,
                'message' => '事务已回滚：' . $e->getMessage(),
                'data' => null,
            ];
        }
    }
}