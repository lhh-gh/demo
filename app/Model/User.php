<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 */
class User extends Model
{
    protected ?string $table = 'users';

    protected array $fillable = [
        'id',
        'name',
        'email',
        'created_at',
        'updated_at',
    ];
}