<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * 业务错误码常量类
 *
 * 作用：
 * - 统一管理项目中的业务错误码
 * - 统一维护错误码对应的默认提示信息
 * - 避免在业务代码中到处写魔法数字
 *
 * 使用方式：
 * ErrorCode::USER_NOT_FOUND
 * ErrorCode::getMessage(ErrorCode::USER_NOT_FOUND)
 */
#[Constants]
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("成功")
     *
     * 表示接口调用成功
     */
    public const SUCCESS = 0;

    /**
     * @Message("服务器内部错误")
     *
     * 表示系统运行时异常或未知错误
     */
    public const SERVER_ERROR = 500;

    /**
     * @Message("用户不存在")
     *
     * 表示查询用户时找不到对应记录
     */
    public const USER_NOT_FOUND = 10001;

    /**
     * @Message("库存不足")
     *
     * 表示扣减库存时库存数量不够
     */
    public const STOCK_NOT_ENOUGH = 10002;

    /**
     * @Message("重复提交订单")
     *
     * 表示用户重复点击提交订单或接口重复请求
     */
    public const ORDER_REPEAT_SUBMIT = 10003;

    /**
     * @Message("参数错误")
     *
     * 表示当前请求参数不符合业务要求
     */
    public const PARAM_INVALID = 10004;
}