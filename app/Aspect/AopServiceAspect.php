<?php

declare(strict_types=1);

namespace App\Aspect;

use App\Service\AopService;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Psr\Log\LoggerInterface;

#[Aspect]
class AopServiceAspect extends AbstractAspect
{
    /**
     * 定义切点
     *
     * 表示只拦截 AopService 类中的 getUserInfo 方法
     */
    public array $classes = [
        AopService::class . '::getUserInfo',
    ];

    /**
     * 注入日志组件
     *
     * 这里使用 PSR 标准日志接口，方便记录 AOP 前后日志
     */
    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    /**
     * AOP 核心处理方法
     *
     * @param ProceedingJoinPoint $proceedingJoinPoint 当前连接点对象
     *
     * 作用：
     * 1. 在目标方法执行前打印日志
     * 2. 统计目标方法执行耗时
     * 3. 在目标方法执行后打印日志
     * 4. 对返回值进行增强
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /**
         * 获取目标方法接收到的参数
         *
         * 例如调用 getUserInfo(1)，这里就能拿到参数 1
         */
        $arguments = $proceedingJoinPoint->getArguments();

        /**
         * 记录方法开始执行时间
         *
         * microtime(true) 返回当前 Unix 时间戳（浮点型，精确到微秒）
         */
        $startTime = microtime(true);

        /**
         * 前置日志
         *
         * 记录：
         * - 当前执行的类名
         * - 当前执行的方法名
         * - 当前方法参数
         */
        $this->logger->info('AOP before', [
            'class' => $proceedingJoinPoint->className,
            'method' => $proceedingJoinPoint->methodName,
            'arguments' => $arguments,
        ]);

        /**
         * 执行原始业务方法
         *
         * 这一句非常关键：
         * 如果不调用 process()，原方法就不会真正执行
         */
        $result = $proceedingJoinPoint->process();

        /**
         * 计算方法执行耗时
         *
         * 单位转成毫秒，保留 2 位小数
         */
        $cost = round((microtime(true) - $startTime) * 1000, 2);

        /**
         * 后置日志
         *
         * 记录：
         * - 原始返回结果
         * - 方法执行耗时
         */
        $this->logger->info('AOP after', [
            'result' => $result,
            'cost_ms' => $cost,
        ]);

        /**
         * 对返回结果进行增强
         *
         * 如果原方法返回的是数组，
         * 就在结果中额外追加一个 aop_cost_ms 字段
         */
        if (is_array($result)) {
            $result['aop_cost_ms'] = $cost;
        }

        /**
         * 返回最终结果
         *
         * 注意：
         * 这里返回的不是一定要原始结果，
         * 也可以返回你修改后的结果
         */
        return $result;
    }
}