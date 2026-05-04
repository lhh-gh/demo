<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Context\Context;
use Hyperf\Coroutine\Coroutine;

class ContextDemoService
{
    public function setUserContext(int $userId): void
    {
        // 把当前用户 ID 放入协程上下文
        Context::set('current_user_id', $userId);

        var_dump('=== ContextDemoService::setUserContext ===');
        var_dump([
            'cid' => Coroutine::id(),
            'current_user_id' => Context::get('current_user_id'),
        ]);
    }

    public function getUserContext(): array
    {
        return [
            'cid' => Coroutine::id(),
            'request_id' => Context::get('request_id'),
            'current_user_id' => Context::get('current_user_id'),
        ];
    }
}