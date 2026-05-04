<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\RequestLifecycleDemoService;
use Hyperf\Context\Context;
use Hyperf\Coroutine\Coroutine;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'lifecycle')]
class RequestLifecycleDemoController
{
    public function __construct(
        protected RequestLifecycleDemoService $service
    ) {
    }

    #[GetMapping('request')]
    public function request(): array
    {
        $cid = Coroutine::id();

        var_dump('Controller start');
        var_dump([
            'cid' => $cid,
            'request_id' => Context::get('request_id'),
        ]);

        $data = $this->service->handle();

        var_dump('Controller end');
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