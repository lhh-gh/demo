<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Context\Context;
use Hyperf\Coroutine\Coroutine;

class RequestLifecycleDemoService
{
    public function handle(): array
    {
        $cid = Coroutine::id();
        $requestId = Context::get('request_id');

        var_dump('Service start');
        var_dump([
            'cid' => $cid,
            'request_id' => $requestId,
        ]);

        // 模拟业务处理中写入协程上下文
        Context::set('current_user_id', 1001);

        // 模拟 IO 场景
// 模拟 IO 场景
        Coroutine::sleep(0.2);

        var_dump('Service end');
        var_dump([
            'cid' => $cid,
            'request_id' => Context::get('request_id'),
            'current_user_id' => Context::get('current_user_id'),
        ]);

        return [
            'cid' => $cid,
            'request_id' => $requestId,
            'current_user_id' => Context::get('current_user_id'),
        ];
    }
}