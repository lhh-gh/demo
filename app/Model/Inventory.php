<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * 库存模型
 *
 * 对应数据表：inventories
 *
 * @property int $id 主键ID
 * @property int $sku_id 商品 SKU ID
 * @property int $stock 库存数量
 * @property string|null $created_at 创建时间
 * @property string|null $updated_at 更新时间
 */
class Inventory extends Model
{
    /**
     * 对应表名
     */
    protected ?string $table = 'inventories';

    /**
     * 允许批量赋值的字段
     */
    protected array $fillable = [
        'sku_id',
        'stock',
        'created_at',
        'updated_at',
    ];

    /**
     * 属性类型转换
     */
    protected array $casts = [
        'id' => 'integer',
        'sku_id' => 'integer',
        'stock' => 'integer',
    ];
}