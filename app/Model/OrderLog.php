<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

class OrderLog extends Model
{
    protected ?string $table = 'order_logs';

    protected array $fillable = [
        'order_id',
        'content',
        'created_at',
        'updated_at',
    ];

    protected array $casts = [
        'id' => 'integer',
        'order_id' => 'integer',
    ];
}