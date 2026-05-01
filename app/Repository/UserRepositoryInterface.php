<?php

declare(strict_types=1);

namespace App\Repository;

interface UserRepositoryInterface
{
    public function findById(int $id): ?array;
}
