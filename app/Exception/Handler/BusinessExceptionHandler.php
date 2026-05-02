<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\BusinessException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * 业务异常处理器
 *
 * 作用：
 * 专门处理项目中主动抛出的 BusinessException，
 * 并统一转换成标准 JSON 返回给前端。
 *
 * 适合处理的场景：
 * - 用户不存在
 * - 库存不足
 * - 重复提交
 * - 权限不足
 *
 * 这个处理器只负责“业务异常”，
 * 不负责 404、405 或系统未知异常。
 */
class BusinessExceptionHandler extends ExceptionHandler
{
    /**
     * 注入 HTTP 响应对象
     *
     * 通过它可以快速返回 JSON 数据
     */
    public function __construct(
        protected HttpResponse $response
    ) {
    }

    /**
     * 异常处理核心方法
     *
     * @param Throwable $throwable 当前捕获到的异常对象
     * @param ResponseInterface $response 当前响应对象
     *
     * 如果当前异常是 BusinessException，
     * 则统一返回标准业务错误 JSON。
     */
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if ($throwable instanceof BusinessException) {
            return $this->response->json([
                // 业务错误码
                'code' => $throwable->getCode(),

                // 业务错误信息
                'message' => $throwable->getMessage(),

                // 业务异常通常返回 null 即可
                'data' => null,
            ]);
        }

        /**
         * 如果不是 BusinessException，
         * 这里不处理，交给后面的异常处理器继续处理
         */
        return $response;
    }

    /**
     * 判断当前处理器是否需要处理该异常
     *
     * 只有 BusinessException 才由当前处理器接管
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof BusinessException;
    }
}