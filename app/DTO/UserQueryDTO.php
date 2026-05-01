<?php

declare(strict_types=1);

namespace App\DTO;
/**
 *  分页DTO
 */
class UserQueryDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $pageSize = 10,
        public readonly ?string $keyword = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            page: (int) ($data['page'] ?? 1),
            pageSize: (int) ($data['page_size'] ?? 10),
            keyword: $data['keyword'] ?? null,
        );
    }
}