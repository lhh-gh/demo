<?php

namespace App\Repository;

use App\Model\Member;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;

class MemberRepository
{
    /**
     * 注入 Redis
     */
    #[Inject]
    protected Redis $redis;

    /**
     * 会员详情缓存 key
     */
    protected function getCacheKey(int $id): string
    {
        return "member:profile:{$id}";
    }

    /**
     * 分布式锁 key
     */
    protected function getLockKey(int $id): string
    {
        return "lock:member:profile:{$id}";
    }

    /**
     * 查询会员详情
     *
     * 包含：
     * 1. 缓存穿透保护
     * 2. 缓存击穿保护
     * 3. 缓存雪崩保护
     */
    public function findByIdWithCacheProtect(int $id): ?array
    {
        $cacheKey = $this->getCacheKey($id);
        $lockKey = $this->getLockKey($id);

        // 1. 先查缓存
        $cached = $this->redis->get($cacheKey);
        if ($cached !== false && $cached !== null) {
            if ($cached === 'null') {
                return null;
            }

            return json_decode($cached, true);
        }

        // 2. 尝试加锁，防止热点 key 击穿
        $locked = $this->redis->set($lockKey, '1', ['nx', 'ex' => 5]);

        // 3. 没抢到锁，短暂等待后重试缓存
        if (! $locked) {
            usleep(50000);

            $retryCached = $this->redis->get($cacheKey);
            if ($retryCached !== false && $retryCached !== null) {
                if ($retryCached === 'null') {
                    return null;
                }

                return json_decode($retryCached, true);
            }

            return null;
        }

        try {
            // 4. 双重检查缓存
            $cachedAgain = $this->redis->get($cacheKey);
            if ($cachedAgain !== false && $cachedAgain !== null) {
                if ($cachedAgain === 'null') {
                    return null;
                }

                return json_decode($cachedAgain, true);
            }

            // 5. 查数据库
            $member = Member::query()->find($id);

            // 6. 防缓存穿透：不存在数据也缓存空值
            if (! $member) {
                $this->redis->setex($cacheKey, 60, 'null');
                return null;
            }

            $data = $member->toArray();

            // 7. 防缓存雪崩：TTL 加随机值
            $ttl = 3600 + random_int(1, 300);

            $this->redis->setex(
                $cacheKey,
                $ttl,
                json_encode($data, JSON_UNESCAPED_UNICODE)
            );

            return $data;
        } finally {
            // 8. 释放锁
            $this->redis->del($lockKey);
        }
    }

    /**
     * 更新会员昵称
     *
     * 使用延迟双删策略：
     * 1. 先删缓存
     * 2. 再更新数据库
     * 3. 延迟一段时间再删一次缓存
     */
    public function updateNicknameWithDelayedDoubleDelete(int $id, string $nickname): bool
    {
        $cacheKey = $this->getCacheKey($id);

        // 第一次删除缓存
        $this->redis->del($cacheKey);

        Db::beginTransaction();

        try {
            $member = Member::query()->find($id);
            if (! $member) {
                Db::rollBack();
                return false;
            }

            $member->nickname = $nickname;
            $member->save();

            Db::commit();
        } catch (\Throwable $throwable) {
            Db::rollBack();
            throw $throwable;
        }

        // 延迟后第二次删除缓存
        usleep(200000);
        $this->redis->del($cacheKey);

        return true;
    }

    /**
     * 普通更新方案
     *
     * 先更新 MySQL
     * 再删除 Redis
     */
    public function updateByIdAndClearCache(int $id, array $data): bool
    {
        Db::beginTransaction();

        try {
            $member = Member::query()->find($id);
            if (! $member) {
                Db::rollBack();
                return false;
            }

            $member->fill($data);
            $member->save();

            $this->redis->del($this->getCacheKey($id));

            Db::commit();
            return true;
        } catch (\Throwable $throwable) {
            Db::rollBack();
            throw $throwable;
        }
    }
}