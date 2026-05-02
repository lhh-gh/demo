<?php

declare(strict_types=1);

namespace App\Exception;

use App\Constants\ErrorCode;
use Hyperf\Server\Exception\ServerException;
use Throwable;

/**
 * 业务异常类
 *
 * 作用：
 * 用于在业务逻辑中主动抛出“业务级错误”。
 *
 * 常见场景：
 * - 用户不存在
 * - 库存不足
 * - 重复提交订单
 * - 参数不符合业务规则
 *
 * 和系统异常不同：
 * 业务异常通常是“程序预期内”的异常情况，
 * 只是业务流程不允许继续执行。
 */
class BusinessException extends ServerException
{
    /**
     * 构造方法
     *
     * @param int $code 业务错误码
     * @param string|null $message 自定义错误消息
     * @param Throwable|null $previous 上一个异常对象
     *
     * 设计说明：
     * - 如果没有传 message，则自动根据错误码去 ErrorCode 中查默认提示
     * - 如果传了自定义 message，则优先使用自定义 message
     */
    public function __construct(int $code = 0, ?string $message = null, ?Throwable $previous = null)
    {
        /**
         * 如果没有传入自定义消息，
         * 则自动根据错误码查找默认错误信息
         */
        if ($message === null) {
            $message = ErrorCode::getMessage($code);
        }

        /**
         * 调用父类 ServerException 构造方法
         *
         * 最终会把：
         * - message
         * - code
         * - previous
         * 保存到异常对象中
         */
        parent::__construct($message, $code, $previous);
    }
}