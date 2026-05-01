<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\UserCreateDTO;
use App\DTO\UserQueryDTO;
use App\DTO\UserUpdateDTO;
use App\Model\User;
use Hyperf\Paginator\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function paginate(UserQueryDTO $dto): LengthAwarePaginator
    {
        return User::query()
            ->when($dto->keyword, function ($query) use ($dto) {
                $query->where('name', 'like', '%' . $dto->keyword . '%')
                    ->orWhere('email', 'like', '%' . $dto->keyword . '%');
            })
            ->orderByDesc('id')
            ->paginate(
                perPage: $dto->pageSize,
                page: $dto->page
            );
    }

    public function findById(int $id): ?User
    {
        return User::query()->find($id);
    }

    public function create(UserCreateDTO $dto): User
    {
        return User::query()->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'age' => $dto->age,
        ]);
    }

    public function update(User $user, UserUpdateDTO $dto): bool
    {
        return $user->update([
            'name' => $dto->name,
            'email' => $dto->email,
            'age' => $dto->age,
        ]);
    }

    public function delete(User $user): bool
    {
        return (bool)$user->delete();
    }
}