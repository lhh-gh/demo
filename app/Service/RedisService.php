<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Redis\Redis;
use Hyperf\Di\Annotation\Inject;

class RedisService
{
    #[Inject]
    protected Redis $redis;
    /**
     * 写缓存
     */
    public function set(string $key, mixed $value, int $ttl = 0): bool
    {
        $value = is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE);

        if ($ttl > 0) {
            return (bool) $this->redis->set($key, $value, $ttl);
        }

        return (bool) $this->redis->set($key, $value);
    }
    /**
     * 读缓存
     */
    public function get(string $key, bool $decodeJson = false): mixed
    {
        $value = $this->redis->get($key);

        if ($value === false || $value === null) {
            return null;
        }

        if ($decodeJson) {
            return json_decode($value, true);
        }

        return $value;
    }

    /**
     * 删缓存
     */
    public function delete(string $key): int
    {
        return $this->redis->del($key);
    }

    public function exists(string $key): bool
    {
        return (bool) $this->redis->exists($key);
    }

    public function expire(string $key, int $ttl): bool
    {
        return (bool) $this->redis->expire($key, $ttl);
    }

    public function incr(string $key, int $by = 1): int
    {
        return $by === 1 ? $this->redis->incr($key) : $this->redis->incrBy($key, $by);
    }

    public function ttl(string $key): int
    {
        return $this->redis->ttl($key);
    }

    public function getClient(): Redis
    {
        return $this->redis;
    }
}