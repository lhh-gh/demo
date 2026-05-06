<?php

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

class Member extends Model
{
    /**
     * 对应数据表
     */
    protected ?string $table = 'members';

    /**
     * 允许批量赋值的字段
     */
    protected array $fillable = [
        'nickname',
        'email',
        'mobile',
        'status',
    ];
}