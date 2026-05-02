<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Redis\Redis;

class RedisLockService
{
    public function __construct(
        protected RedisService $redisService
    ) {
    }

    public function acquire(string $key, string $token, int $ttl = 10): bool
    {
        /** @var Redis $redis */
        $redis = $this->redisService->getClient();

        return (bool) $redis->set($key, $token, ['nx', 'ex' => $ttl]);
    }

    public function release(string $key, string $token): bool
    {
        $lua = 'if redis.call("get", KEYS[1]) == ARGV[1] then                                                                                                                        
          return redis.call("del", KEYS[1])                                                                                                                                        
      else                                                                                                                                                                         
          return 0                                                                                                                                                                 
      end';

        $result = $this->redisService->getClient()->eval($lua, [$key, $token], 1);

        return (int) $result === 1;
    }

    public function executeWithLock(string $key, callable $callback, int $ttl = 10): mixed
    {
        $token = uniqid('', true);

        if (! $this->acquire($key, $token, $ttl)) {
            return [
                'code' => 423,
                'message' => '操作过于频繁，请稍后再试',
                'data' => null,
            ];
        }

        try {
            return $callback();
        } finally {
            $this->release($key, $token);
        }
    }
}