<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\DbConnection\Db;

class DemoUserQueryService
{
    /**
     * 查询全部用户
     */
    public function all(): array
    {
        return Db::table('demo_users')->get()->toArray();
    }

    /**
     * 按 ID 查询
     */
    public function findById(int $id): ?object
    {
        return Db::table('demo_users')
            ->where('id', $id)
            ->first();
    }

    /**
     * 查询正常状态用户
     */
    public function activeUsers(): array
    {
        return Db::table('demo_users')
            ->where('status', 1)
            ->get()
            ->toArray();
    }

    /**
     * 查询年龄大于指定值的用户
     */
    public function olderThan(int $age): array
    {
        return Db::table('demo_users')
            ->where('age', '>', $age)
            ->orderBy('age', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * 新增用户
     */
    public function createUser(string $username, string $email, int $age, int $status = 1): int
    {
        return (int) Db::table('demo_users')->insertGetId([
            'username' => $username,
            'email' => $email,
            'age' => $age,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 更新状态
     */
    public function updateStatus(int $id, int $status): int
    {
        return Db::table('demo_users')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * 删除用户
     */
    public function deleteUser(int $id): int
    {
        return Db::table('demo_users')
            ->where('id', $id)
            ->delete();
    }
}