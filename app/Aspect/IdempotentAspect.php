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
     * 按自定义注解切入
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

        $reflectionMethod = ReflectionManager::reflectMethod($className, $methodName);
        $attributes = $reflectionMethod->getAttributes(Idempotent::class);

        if (empty($attributes)) {
            return $proceedingJoinPoint->process();
        }

        /** @var Idempotent $annotation */
        $annotation = $attributes[0]->newInstance();

        /** @var ServerRequestInterface|null $request */
        $request = Context::get(ServerRequestInterface::class);

        if (! $request) {
            return $proceedingJoinPoint->process();
        }

        $idempotencyKey = $request->getHeaderLine('Idempotency-Key');

        if ($idempotencyKey === '') {
            return [
                'code' => 400,
                'message' => '缺少 Idempotency-Key 请求头',
                'data' => null,
            ];
        }

        $redisKey = sprintf(
            '%s:%s:%s',
            $annotation->prefix,
            $methodName,
            $idempotencyKey
        );

        // 原子幂等校验：SET key value NX EX ttl
        $success = $this->redisService->getClient()->set(
            $redisKey,
            1,
            ['nx', 'ex' => $annotation->ttl]
        );

        if (! $success) {
            return [
                'code' => 409,
                'message' => '重复请求，请勿重复提交',
                'data' => null,
            ];
        }

        return $proceedingJoinPoint->process();
    }
}