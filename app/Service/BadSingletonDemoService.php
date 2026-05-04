<?php

declare(strict_types=1);

namespace App\Service;

class BadSingletonDemoService
{
    /**
     * 错误示范：
     * 这个属性如果挂在常驻对象上，可能被不同请求共用
     */
    protected ?int $currentUserId = null;

    public function setCurrentUserId(int $userId): void
    {
        $this->currentUserId = $userId;
    }

    public function getCurrentUserId(): ?int
    {
        return $this->currentUserId;
    }
}