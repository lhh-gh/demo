<?php

declare(strict_types=1);

namespace App\DTO;
/**
 *  修改DTO
 */
class UserUpdateDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly int $age
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            email: (string) $data['email'],
            age: (int) $data['age']
        );
    }
}