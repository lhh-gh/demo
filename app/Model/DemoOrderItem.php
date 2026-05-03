<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

class DemoOrderItem extends Model
{
    protected ?string $table = 'order_items';

    protected array $fillable = [
        'order_id',
        'sku_id',
        'product_name',
        'price',
        'quantity',
        'amount',
        'created_at',
        'updated_at',
    ];

    protected array $casts = [
        'id' => 'integer',
        'order_id' => 'integer',
        'sku_id' => 'integer',
        'quantity' => 'integer',
    ];
}