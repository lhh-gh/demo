<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * DemoArticle 模型
 *
 * 对应表：demo_articles
 */
class DemoArticle extends Model
{
    /**
     * 表名
     */
    protected ?string $table = 'demo_articles';

    /**
     * 允许批量赋值的字段
     */
    protected array $fillable = [
        'title',
        'content',
        'author',
        'status',
        'created_at',
        'updated_at',
    ];
}