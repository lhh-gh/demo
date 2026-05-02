<?php
declare(strict_types=1);

namespace App\Aspect;

use App\Service\AopService;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;

#[Aspect]
class AopServiceAspect extends AbstractAspect
{
    // 这个是切 口
    public array $classes = [
        AopService::class . '::getUserInfo',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // 方法参数
        $arguments = $proceedingJoinPoint->getArguments();

        var_dump('AOP before', $arguments);

        // 执行原方法
        $result = $proceedingJoinPoint->process();

        var_dump('AOP after', $result);

        return $result;
    }
}