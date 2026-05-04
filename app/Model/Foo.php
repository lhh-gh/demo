<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * Foo 模型
 */
class Foo extends Model
{
    protected ?string $table = 'foo_articles';

    protected array $fillable = [
        'title',
        'content',
        'author_name',
        'status',
        'created_at',
        'updated_at',
    ];

    protected array $casts = [
        'id' => 'integer',
        'status' => 'integer',
    ];
}