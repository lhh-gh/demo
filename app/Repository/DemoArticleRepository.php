<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\DemoArticle;

/**
 * DemoArticleRepository
 *
 * 负责数据访问层，只和数据库打交道
 */
class DemoArticleRepository
{
    /**
     * 查询全部文章
     */
    public function getAll(): array
    {
        return DemoArticle::query()
            ->orderByDesc('id')
            ->get()
            ->toArray();
    }

    /**
     * 根据 ID 查询文章
     */
    public function findById(int $id): ?DemoArticle
    {
        return DemoArticle::query()->find($id);
    }

    /**
     * 新增文章
     */
    public function create(array $data): DemoArticle
    {
        return DemoArticle::query()->create($data);
    }

    /**
     * 更新文章
     */
    public function updateById(int $id, array $data): bool
    {
        return (bool) DemoArticle::query()
            ->where('id', $id)
            ->update($data);
    }

    /**
     * 删除文章
     */
    public function deleteById(int $id): bool
    {
        return (bool) DemoArticle::query()
            ->where('id', $id)
            ->delete();
    }
}