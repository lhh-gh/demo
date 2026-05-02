<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * 应用全局异常兜底处理器
 *
 * 作用：
 * 处理所有前面没有接住的异常，
 * 作为整个项目 HTTP 异常处理链的最后一道兜底。
 *
 * 适合处理：
 * - TypeError
 * - RuntimeException
 * - SQL 异常
 * - 空对象调用
 * - 未知系统异常
 *
 * 这个处理器通常不区分业务类型，
 * 只负责统一隐藏系统异常细节，避免直接暴露给前端。
 */
class AppExceptionHandler extends ExceptionHandler
{


    /**
     * 注入标准输出日志组件
     *
     * StdoutLoggerInterface 是 Hyperf 提供的标准输出日志接口，
     * 常用于在控制台、Docker 日志、Supervisor 日志中打印错误信息。
     */
    public function __construct(protected StdoutLoggerInterface $logger)
    {
    }
    /**
     * 注入 HTTP 响应对象
     */
//    public function __construct(
//        protected HttpResponse $response
//    ) {
//    }

    /**
     * 异常处理方法
     *
     * @param Throwable $throwable 当前异常对象
     * @param ResponseInterface $response 响应对象
     *
     * 这里一般做两件事：
     * 1. 记录系统异常日志
     * 2. 返回统一的系统错误 JSON
     */
//    public function handle(Throwable $throwable, ResponseInterface $response)
//    {
//        /**
//         * 这里建议真实项目中记录日志
//         *
//         * 例如：
//         * logger()->error($throwable->getMessage(), [
//         *     'exception' => $throwable,
//         * ]);
//         */
//        // logger()->error($throwable->getMessage(), ['exception' => $throwable]);
//
//        return $this->response->json([
//            // 系统异常统一返回 500
//            'code' => 500,
//
//            // 不要把底层异常细节直接暴露给前端
//            'message' => '服务器内部错误',
//
//            'data' => null,
//        ]);
//    }

    /**
     * 异常处理核心方法
     *
     * @param Throwable $throwable 当前捕获到的异常对象
     * @param ResponseInterface $response 当前响应对象
     *
     * 这里主要做三件事：
     * 1. 记录异常简要信息
     * 2. 记录完整异常堆栈
     * 3. 返回统一的 500 响应
     */
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        /**
         * 记录异常简要信息
         *
         * 输出内容包括：
         * - 异常消息
         * - 出错行号
         * - 出错文件
         *
         * 例如：
         * Call to a member function foo() on null[25] in /app/Service/UserService.php
         */
        $this->logger->error(sprintf(
            '%s[%s] in %s',
            $throwable->getMessage(),
            $throwable->getLine(),
            $throwable->getFile()
        ));

        /**
         * 记录完整异常堆栈
         *
         * 这样可以看到：
         * - 异常从哪里抛出
         * - 调用了哪些类和方法
         * - 整个调用链路是什么
         *
         * 对排查线上问题非常有帮助。
         */
        $this->logger->error($throwable->getTraceAsString());

        /**
         * 返回统一的 HTTP 500 响应
         *
         * 这里没有把真实异常信息直接暴露给前端，
         * 而是返回固定文案：Internal Server Error.
         *
         * 这样做的好处：
         * - 避免泄露系统内部实现细节
         * - 保证前端看到的是统一错误提示
         */
        return $response
            ->withHeader('Server', 'Hyperf')
            ->withStatus(500)
            ->withBody(new SwooleStream('Internal Server Error.'));
    }
    /**
     * 判断当前处理器是否有效
     *
     * 这里直接返回 true，
     * 表示它作为兜底处理器，愿意处理所有异常。
     */
    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}