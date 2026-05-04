<?php

declare(strict_types=1);

namespace App\Service;

class BadDemoService
{
    /*** 错误示范
     * @var int|null
     */
    protected ?int $currentUserId = null;

    public function setUserId(int $userId): void
    {
        $this->currentUserId = $userId;
    }

    public function getUserId(): ?int
    {
        return $this->currentUserId;
    }
}