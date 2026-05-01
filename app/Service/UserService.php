<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\UserCreateDTO;
use App\DTO\UserQueryDTO;
use App\DTO\UserUpdateDTO;
use App\Repository\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {
    }

    public function list(UserQueryDTO $dto): array
    {
        $pager = $this->userRepository->paginate($dto);

        return [
            'list' => array_map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'email' => $item->email,
                'age' => $item->age,
                'created_at' => (string) $item->created_at,
            ], $pager->items()),
            'pagination' => [
                'total' => $pager->total(),
                'page' => $pager->currentPage(),
                'page_size' => $pager->perPage(),
                'last_page' => $pager->lastPage(),
            ],
        ];
    }

    public function show(int $id): ?array
    {
        $user = $this->userRepository->findById($id);

        if (! $user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'age' => $user->age,
            'created_at' => (string) $user->created_at,
            'updated_at' => (string) $user->updated_at,
        ];
    }

    public function create(UserCreateDTO $dto): array
    {
        $user = $this->userRepository->create($dto);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'age' => $user->age,
        ];
    }

    public function update(int $id, UserUpdateDTO $dto): ?array
    {
        $user = $this->userRepository->findById($id);

        if (! $user) {
            return null;
        }

        $this->userRepository->update($user, $dto);

        $user->refresh();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'age' => $user->age,
        ];
    }

    public function delete(int $id): bool
    {
        $user = $this->userRepository->findById($id);

        if (! $user) {
            return false;
        }

        return $this->userRepository->delete($user);
    }
}