<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    )
    {
    }

    public function getUserInfo(int $id): array
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return [
                'code' => 404,
                'message' => '用户不存在',
                'data' => null,
            ];
        }

        return [
            'code' => 0,
            'message' => 'success',
            'data' => $user,
        ];
    }
}