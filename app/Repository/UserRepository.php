<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?array
    {
        $user = User::query()->find($id);

        if (!$user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}