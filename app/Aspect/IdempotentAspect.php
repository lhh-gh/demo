<?php

declare(strict_types=1);

namespace App\Aspect;

use App\Annotation\Idempotent;
use App\Service\RedisService;
use Hyperf\Context\Context;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\ReflectionManager;
use Psr\Http\Message\ServerRequestInterface;

#[Aspect]
class IdempotentAspect extends AbstractAspect
{
    /**
     * 按注解切入
     */
    public array $annotations = [
        Idempotent::class,
    ];

    public function __construct(
        protected RedisService $redisService
    ) {
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $className = $proceedingJoinPoint->className;
        $methodName = $proceedingJoinPoint->methodName;

        // 反射当前方法，读取注解
        $reflectionMethod = ReflectionManager::reflectMethod($className, $methodName);
        $attributes = $reflectionMethod->getAttributes(Idempotent::class);

        if (empty($attributes)) {
            return $proceedingJoinPoint->process();
        }

        /** @var Idempotent $annotation */
        $annotation = $attributes[0]->newInstance();

        // 获取当前请求对象
        /** @var ServerRequestInterface|null $request */
        $request = Context::get(ServerRequestInterface::class);

        if (! $request) {
            return $proceedingJoinPoint->process();
        }

        // 从请求头中获取幂等标识
        $idempotencyKey = $request->getHeaderLine('Idempotency-Key');

        if ($idempotencyKey === '') {
            return [
                'code' => 400,
                'message' => '缺少 Idempotency-Key 请求头',
                'data' => null,
            ];
        }

        // 生成 Redis 幂等 key
        $redisKey = sprintf(
            '%s:%s:%s',
            $annotation->prefix,
            $methodName,
            $idempotencyKey
        );

        // 如果 key 已存在，说明重复请求
        if ($this->redisService->exists($redisKey)) {
            return [
                'code' => 409,
                'message' => '重复请求，请勿重复提交',
                'data' => null,
            ];
        }

        // 写入 Redis，设置 TTL
        $this->redisService->set($redisKey, 1, $annotation->ttl);

        // 执行业务方法
        return $proceedingJoinPoint->process();
    }
}