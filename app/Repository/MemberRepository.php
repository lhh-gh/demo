<?php

namespace App\Repository;

use App\Model\Member;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;

class MemberRepository
{
    /**
     * 注入 Redis 客户端
     */
    #[Inject]
    protected Redis $redis;

    /**
     * 获取会员详情缓存 Key
     *
     * 例如：
     * member:profile:1
     */
    protected function getCacheKey(int $id): string
    {
        return "member:profile:{$id}";
    }

    /**
     * 根据会员ID查询会员信息（优先读取缓存）
     *
     * 查询流程：
     * 1. 先查 Redis
     * 2. Redis 有数据直接返回
     * 3. Redis 没有数据则查 MySQL
     * 4. MySQL 查到数据后回写 Redis
     * 5. 如果数据库也没有，则缓存空值，防止缓存穿透
     *
     * @param int $id 会员ID
     * @return array|null
     */
    public function findByIdWithCache(int $id): ?array
    {
        // 生成缓存 Key
        $cacheKey = $this->getCacheKey($id);

        // 先从 Redis 中获取缓存数据
        $cached = $this->redis->get($cacheKey);

        // 如果缓存存在，直接返回
        if ($cached !== false && $cached !== null) {
            // 如果缓存的是字符串 null，表示数据库中没有该会员
            if ($cached === 'null') {
                return null;
            }

            // 将 JSON 字符串转换为数组后返回
            return json_decode($cached, true);
        }

        // Redis 中没有数据，则查询 MySQL
        $member = Member::query()->find($id);

        // 如果数据库中也没有该会员，缓存空值，避免缓存穿透
        if (! $member) {
            $this->redis->setex($cacheKey, 60, 'null');
            return null;
        }

        // 将模型对象转换为数组
        $data = $member->toArray();

        // 把数据库中的结果写入 Redis，缓存 3600 秒
        $this->redis->setex(
            $cacheKey,
            3600,
            json_encode($data, JSON_UNESCAPED_UNICODE)
        );

        // 返回会员数据
        return $data;
    }

    /**
     * 根据会员ID更新会员信息，并删除对应缓存
     *
     * 缓存一致性策略：
     * 1. 先更新 MySQL
     * 2. 再删除 Redis 缓存
     *
     * 这样可以保证下次读取时重新从数据库加载最新数据
     *
     * @param int $id 会员ID
     * @param array $data 需要更新的数据
     * @return bool
     * @throws \Throwable
     */
    public function updateByIdAndClearCache(int $id, array $data): bool
    {
        // 开启数据库事务
        Db::beginTransaction();

        try {
            // 查询会员是否存在
            $member = Member::query()->find($id);

            // 如果会员不存在，则回滚事务并返回失败
            if (! $member) {
                Db::rollBack();
                return false;
            }

            // 填充更新数据
            $member->fill($data);

            // 保存到数据库
            $member->save();

            // 删除对应缓存，保证缓存和数据库最终一致
            $this->redis->del($this->getCacheKey($id));

            // 提交事务
            Db::commit();
            return true;
        } catch (\Throwable $throwable) {
            // 发生异常时回滚事务
            Db::rollBack();
            throw $throwable;
        }
    }
}