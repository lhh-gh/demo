<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * 事务业务错误码常量类
 *
 * 作用：
 * - 统一管理事务场景下的业务错误码
 * - 统一维护错误码对应的默认提示信息
 * - 避免在业务代码中到处写魔法数字
 *
 * 使用方式：
 * TransactionErrorCode::STOCK_NOT_ENOUGH
 * TransactionErrorCode::getMessage(TransactionErrorCode::STOCK_NOT_ENOUGH)
 */
#[Constants]
class TransactionErrorCode extends AbstractConstants
{
    /**
     * @Message("成功")
     */
    public const SUCCESS = 0;

    /**
     * @Message("服务器内部错误")
     */
    public const SERVER_ERROR = 500;

    /**
     * @Message("库存记录不存在")
     */
    public const INVENTORY_NOT_FOUND = 20001;

    /**
     * @Message("库存不足")
     */
    public const STOCK_NOT_ENOUGH = 20002;

    /**
     * @Message("购买数量必须大于 0")
     */
    public const ORDER_QUANTITY_INVALID = 20003;

    /**
     * @Message("订单创建失败")
     */
    public const ORDER_CREATE_FAILED = 20004;

    /**
     * @Message("重复提交")
     */
    public const ORDER_REPEAT_SUBMIT=20005;
}