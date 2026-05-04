<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Foo;

class FooRepository
{
    /**
     * 查询列表
     */
    public function getList(int $page = 1, int $pageSize = 10): array
    {
        return Foo::query()
            ->orderByDesc('id')
            ->forPage($page, $pageSize)
            ->get()
            ->toArray();
    }

    /**
     * 查询总数
     */
    public function getTotal(): int
    {
        return Foo::query()->count();
    }

    /**
     * 查询详情
     */
    public function findById(int $id): ?Foo
    {
        return Foo::query()->find($id);
    }

    /**
     * 创建数据
     */
    public function create(array $data): Foo
    {
        return Foo::query()->create($data);
    }
}