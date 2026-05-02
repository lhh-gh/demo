<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\DemoProduct;
use App\Service\RedisService;

/**
 * Repository 负责：
 *
 * - 查数据库
 * - 查缓存
 * - 回填缓存
 * - 更新后删缓存
 */
class DemoProductRepository
{
    public function __construct(
        protected RedisService $redisService
    ) {
    }

    /**
     * 商品详情缓存 key
     */
    protected function getDetailCacheKey(int $id): string
    {
        return "demo_product:detail:{$id}";
    }

    /**
     * 查询商品详情：先查缓存，没有再查数据库
     */
    public function findByIdWithCache(int $id): ?array
    {
        $cacheKey = $this->getDetailCacheKey($id);

        // 1. 先查缓存
        $cached = $this->redisService->get($cacheKey, true);
        if ($cached !== null) {
            return $cached;
        }

        // 2. 缓存没有，查数据库
        $product = DemoProduct::query()->find($id);
        if (! $product) {
            return null;
        }

        $data = $product->toArray();

        // 3. 回填缓存，缓存 10 分钟
        $this->redisService->set($cacheKey, $data, 600);

        return $data;
    }

    /**
     * 查询全部商品（这里演示直接查库，不做列表缓存）
     */
    public function getAll(): array
    {
        return DemoProduct::query()
            ->orderByDesc('id')
            ->get()
            ->toArray();
    }

    /**
     * 新增商品
     */
    public function create(array $data): DemoProduct
    {
        return DemoProduct::query()->create($data);
    }

    /**
     * 更新商品
     *
     * 先更新数据库，再删缓存
     */
    public function updateById(int $id, array $data): bool
    {
        $result = (bool) DemoProduct::query()
            ->where('id', $id)
            ->update($data);

        if ($result) {
            $this->redisService->delete($this->getDetailCacheKey($id));
        }

        return $result;
    }

    /**
     * 删除商品
     *
     * 先删数据库，再删缓存
     */
    public function deleteById(int $id): bool
    {
        $result = (bool) DemoProduct::query()
            ->where('id', $id)
            ->delete();

        if ($result) {
            $this->redisService->delete($this->getDetailCacheKey($id));
        }

        return $result;
    }
}