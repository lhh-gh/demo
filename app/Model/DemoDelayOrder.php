<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * 延迟关闭订单 Demo 模型
 */
class DemoDelayOrder extends Model
{
    protected ?string $table = 'demo_delay_orders';

    protected array $fillable = [
        'order_no',
        'user_id',
        'amount',
        'status',
        'created_at',
        'updated_at',
    ];

    protected array $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'amount' => 'float',
        'status' => 'integer',
    ];
}