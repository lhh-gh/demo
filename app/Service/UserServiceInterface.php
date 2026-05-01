<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\UserCreateDTO;
use App\DTO\UserQueryDTO;
use App\DTO\UserUpdateDTO;

interface UserServiceInterface
{
    public function list(UserQueryDTO $dto): array;

    public function show(int $id): ?array;

    public function create(UserCreateDTO $dto): array;

    public function update(int $id, UserUpdateDTO $dto): ?array;

    public function delete(int $id): bool;
}