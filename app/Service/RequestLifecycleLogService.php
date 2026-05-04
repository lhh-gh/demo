<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Context\Context;
use Hyperf\Coroutine\Coroutine;

class RequestLifecycleLogService
{
    public function handle(): array
    {
        $cid = Coroutine::id();
        $requestId = Context::get('request_id');

        var_dump('=== Service start ===');
        var_dump([
            'cid' => $cid,
            'request_id' => $requestId,
        ]);

        // 模拟业务处理
        Coroutine::sleep(0.2);

        var_dump('=== Service end ===');
        var_dump([
            'cid' => $cid,
            'request_id' => $requestId,
        ]);

        return [
            'cid' => $cid,
            'request_id' => $requestId,
            'message' => 'service done',
        ];
    }
}