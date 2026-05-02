<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * 商品模型
 */
class DemoProduct extends Model
{
    protected ?string $table = 'demo_products';

    protected array $fillable = [
        'name',
        'price',
        'stock',
        'status',
        'created_at',
        'updated_at',
    ];
}