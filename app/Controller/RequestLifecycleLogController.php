<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\RequestLifecycleLogService;
use Hyperf\Context\Context;
use Hyperf\Coroutine\Coroutine;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'lifecycle')]
class RequestLifecycleLogController
{
    public function __construct(
        protected RequestLifecycleLogService $service
    ) {
    }

    #[GetMapping('request-log')]
    public function index(): array
    {
        $cid = Coroutine::id();

        var_dump('=== Controller start ===');
        var_dump([
            'cid' => $cid,
            'request_id' => Context::get('request_id'),
        ]);

        $data = $this->service->handle();

        var_dump('=== Controller end ===');
        var_dump([
            'cid' => $cid,
            'request_id' => Context::get('request_id'),
        ]);

        return [
            'code' => 0,
            'message' => 'success',
            'data' => $data,
        ];
    }
}