<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ContextDemoService;
use Hyperf\Context\Context;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'context-demo')]
class ContextDemoController
{
    public function __construct(
        protected ContextDemoService $service
    ) {
    }

    #[GetMapping('tests')]
    public function test(): array
    {
        // 模拟登录用户
        $this->service->setUserContext(1001);

        $data = $this->service->getUserContext();

        return [
            'code' => 0,
            'message' => 'success',
            'data' => [
                'request_id' => Context::get('request_id'),
                'context' => $data,
            ],
        ];
    }
}