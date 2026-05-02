<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\DemoProduct;
use App\Service\RedisService;

class DemoProductRepository
{
    public function __construct(
        protected RedisService $redisService
    ) {
    }

    protected function getDetailCacheKey(int $id): string
    {
        return "demo_product:detail:{$id}";
    }

    protected function getListCacheKey(): string
    {
        return 'demo_product:list:all';
    }

    protected function getMutexKey(int $id): string
    {
        return "demo_product:mutex:{$id}";
    }

    /**
     * 随机 TTL，缓解缓存雪崩
     */
    protected function randomTtl(int $base): int
    {
        return $base + random_int(30, 180);
    }

    /**
     * 列表缓存
     */
    public function getAllWithCache(): array
    {
        $cacheKey = $this->getListCacheKey();
        $cached = $this->redisService->get($cacheKey, true);

        if (is_array($cached)) {
            return $cached;
        }

        $data = DemoProduct::query()
            ->where('status', 1)
            ->orderByDesc('id')
            ->get()
            ->toArray();

        $this->redisService->set($cacheKey, $data, $this->randomTtl(300));

        return $data;
    }

    /**
     * 详情缓存 + 穿透 + 击穿 + 雪崩保护
     */
    public function findByIdWithCache(int $id): ?array
    {
        $cacheKey = $this->getDetailCacheKey($id);
        $cached = $this->redisService->get($cacheKey, true);

        // 命中正常缓存
        if (is_array($cached)) {
            return $cached;
        }

        // 命中空值缓存，防穿透
        if ($cached === '__NULL__') {
            return null;
        }

        $lockKey = $this->getMutexKey($id);
        $locked = $this->redisService->getClient()->set($lockKey, 1, ['nx', 'ex' => 5]);

        if (! $locked) {
            usleep(100 * 1000);

            $retry = $this->redisService->get($cacheKey, true);

            if (is_array($retry)) {
                return $retry;
            }

            if ($retry === '__NULL__') {
                return null;
            }
        }

        try {
            $product = DemoProduct::query()->find($id);

            // 空值缓存，防穿透
            if (! $product) {
                $this->redisService->set($cacheKey, '__NULL__', 60);
                return null;
            }

            $data = $product->toArray();

            // 随机 TTL，防雪崩
            $this->redisService->set($cacheKey, $data, $this->randomTtl(600));

            return $data;
        } finally {
            $this->redisService->delete($lockKey);
        }
    }
}