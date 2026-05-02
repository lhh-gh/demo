<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\DemoArticleRepository;

/**
 * DemoArticleService
 *
 * 负责业务逻辑层
 */
class DemoArticleService
{
    public function __construct(
        protected DemoArticleRepository $repository
    ) {
    }

    /**
     * 获取文章列表
     */
    public function getList(): array
    {
        return $this->repository->getAll();
    }

    /**
     * 获取文章详情
     */
    public function getDetail(int $id): ?array
    {
        $article = $this->repository->findById($id);

        return $article?->toArray();
    }

    /**
     * 创建文章
     */
    public function create(string $title, string $content, string $author, int $status = 1): array
    {
        $article = $this->repository->create([
            'title' => $title,
            'content' => $content,
            'author' => $author,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $article->toArray();
    }

    /**
     * 更新文章
     */
    public function update(int $id, string $title, string $content, string $author, int $status): bool
    {
        return $this->repository->updateById($id, [
            'title' => $title,
            'content' => $content,
            'author' => $author,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 删除文章
     */
    public function delete(int $id): bool
    {
        return $this->repository->deleteById($id);
    }
}