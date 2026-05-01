<?php

declare(strict_types=1);

namespace App\Repository;
use App\DTO\UserCreateDTO;
use App\DTO\UserQueryDTO;
use App\DTO\UserUpdateDTO;
use App\Model\User;
use Hyperf\Paginator\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function paginate(UserQueryDTO $dto): LengthAwarePaginator;

    public function findById(int $id): ?User;

    public function create(UserCreateDTO $dto): User;

    public function update(User $user, UserUpdateDTO $dto): bool;

    public function delete(User $user): bool;
}
