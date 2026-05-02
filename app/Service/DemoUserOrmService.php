<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\DemoUser;

class DemoUserOrmService
{
    /**
     * 查询全部用户
     */
    public function all(): array
    {
        return DemoUser::query()->get()->toArray();
    }

    /**
     * 按 ID 查询
     */
    public function findById(int $id): ?array
    {
        $user = DemoUser::query()->find($id);

        return $user?->toArray();
    }

    /**
     * 查询正常状态用户
     */
    public function activeUsers(): array
    {
        return DemoUser::query()
            ->where('status', 1)
            ->get()
            ->toArray();
    }

    /**
     * 查询年龄大于指定值的用户
     */
    public function olderThan(int $age): array
    {
        return DemoUser::query()
            ->where('age', '>', $age)
            ->orderBy('age', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * 新增用户
     */
    public function createUser(string $username, string $email, int $age, int $status = 1): array
    {
        $user = DemoUser::query()->create([
            'username' => $username,
            'email' => $email,
            'age' => $age,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $user->toArray();
    }

    /**
     * 更新用户状态
     */
    public function updateStatus(int $id, int $status): bool
    {
        return (bool) DemoUser::query()
            ->where('id', $id)
            ->update([
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * 删除用户
     */
    public function deleteUser(int $id): bool
    {
        return (bool) DemoUser::query()
            ->where('id', $id)
            ->delete();
    }
}