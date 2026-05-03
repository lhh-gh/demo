<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

class DemoOrder extends Model
{
    protected ?string $table = 'orders';

    protected array $fillable = [
        'order_no',
        'user_id',
        'total_amount',
        'status',
        'remark',
        'created_at',
        'updated_at',
    ];

    protected array $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'status' => 'integer',
    ];
}