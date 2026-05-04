<?php

declare(strict_types=1);

namespace App\Aspect;

use App\Service\LifecycleDemoService;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Psr\Log\LoggerInterface;

/**
 *  AOP Demo
 */
#[Aspect]
class LifecycleDemoAspect extends AbstractAspect
{
    public array $classes = [
        LifecycleDemoService::class . '::handle',
    ];

    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $this->logger->info('5. AOP before LifecycleDemoAspect');

        $result = $proceedingJoinPoint->process();

        $this->logger->info('5. AOP after LifecycleDemoAspect');

        return $result;
    }
}
