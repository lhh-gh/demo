<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * DemoUser 模型
 *
 * 对应表：demo_users
 */
class DemoUser extends Model
{
    /**
     * 表名
     */
    protected ?string $table = 'demo_users';

    /**
     * 可批量赋值字段
     */
    protected array $fillable = [
        'username',
        'email',
        'age',
        'status',
        'created_at',
        'updated_at',
    ];
}