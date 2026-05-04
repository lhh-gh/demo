<?php

declare(strict_types=1);

namespace App\Job;

use App\Service\RedisService;
use Hyperf\AsyncQueue\Job;
use Hyperf\Context\ApplicationContext;

class IdempotentCouponJob extends Job
{
    protected int $maxAttempts = 2;

    public function __construct(
        public int $userId,
        public string $requestId
    ) {
    }

    public function handle(): void
    {
        /** @var RedisService $redisService */
        $redisService = ApplicationContext::getContainer()->get(RedisService::class);
        $redis = $redisService->getClient();

        $key = "queue:idempotent:coupon:{$this->userId}:{$this->requestId}";

        // 幂等校验：只有第一次能成功 set nx
        $ok = $redis->set($key, 1, ['nx', 'ex' => 3600]);

        if (! $ok) {
            var_dump('=== IdempotentCouponJob duplicate ===');
            var_dump([
                'user_id' => $this->userId,
                'request_id' => $this->requestId,
                'message' => '重复消费，直接跳过',
            ]);
            return;
        }

        // 模拟发券逻辑
        var_dump('=== IdempotentCouponJob handle ===');
        var_dump([
            'user_id' => $this->userId,
            'request_id' => $this->requestId,
            'message' => '发券成功（模拟）',
        ]);
    }
}