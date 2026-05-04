<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\FooRepository;

class FooService
{
    public function __construct(
        protected FooRepository $repository
    ) {
    }

    /**
     * 列表业务
     */
    public function getList(int $page = 1, int $pageSize = 10): array
    {
        $list = $this->repository->getList($page, $pageSize);
        $total = $this->repository->getTotal();

        return [
            'list' => $list,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'page_size' => $pageSize,
            ],
        ];
    }

    /**
     * 详情业务
     */
    public function getDetail(int $id): ?array
    {
        $foo = $this->repository->findById($id);

        return $foo?->toArray();
    }

    /**
     * 创建业务
     */
    public function create(string $title, string $content, string $authorName): array
    {
        $foo = $this->repository->create([
            'title' => $title,
            'content' => $content,
            'author_name' => $authorName,
            'status' => 1,
        ]);

        return $foo->toArray();
    }
}