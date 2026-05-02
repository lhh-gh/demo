<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\DemoProductRepository;

class DemoProductService
{
    public function __construct(
        protected DemoProductRepository $repository
    ) {
    }

    /**
     * 商品列表
     */
    public function getList(): array
    {
        return $this->repository->getAll();
    }

    /**
     * 商品详情（带缓存）
     */
    public function getDetail(int $id): ?array
    {
        return $this->repository->findByIdWithCache($id);
    }

    /**
     * 创建商品
     */
    public function create(string $name, float $price, int $stock, int $status = 1): array
    {
        $product = $this->repository->create([
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $product->toArray();
    }

    /**
     * 更新商品
     */
    public function update(int $id, string $name, float $price, int $stock, int $status): bool
    {
        return $this->repository->updateById($id, [
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 删除商品
     */
    public function delete(int $id): bool
    {
        return $this->repository->deleteById($id);
    }
}