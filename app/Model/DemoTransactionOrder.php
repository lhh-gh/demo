<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * 事务订单模型
 *
 * 对应数据表：demo_transaction_orders
 *
 * @property int $id 主键ID
 * @property string $order_no 订单号
 * @property int $sku_id 商品 SKU ID
 * @property int $quantity 购买数量
 * @property int $status 订单状态
 * @property string|null $remark 备注
 * @property string|null $created_at 创建时间
 * @property string|null $updated_at 更新时间
 */
class DemoTransactionOrder extends Model
{
    /**
     * 对应表名
     */
    protected ?string $table = 'demo_transaction_orders';

    /**
     * 允许批量赋值的字段
     */
    protected array $fillable = [
        'order_no',
        'sku_id',
        'quantity',
        'status',
        'remark',
        'created_at',
        'updated_at',
    ];

    /**
     * 属性类型转换
     */
    protected array $casts = [
        'id' => 'integer',
        'sku_id' => 'integer',
        'quantity' => 'integer',
        'status' => 'integer',
    ];
}