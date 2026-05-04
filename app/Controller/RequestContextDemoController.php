<?php

declare(strict_types=1);

namespace App\Controller;

use App\Middleware\RequestContextDemoMiddleware;
use App\Service\RequestContextDemoService;
use Hyperf\Context\Context;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Psr\Log\LoggerInterface;

#[Controller(prefix: 'context-demo')]
#[Middleware(RequestContextDemoMiddleware::class)]
class RequestContextDemoController
{
    public function __construct(
        protected LoggerInterface $logger,
        protected RequestContextDemoService $service
    ) {
    }

    #[GetMapping('test')]
    public function test(): array
    {
        $requestId = Context::get('request_id');

        $this->logger->info('Controller read context', [
            'request_id' => $requestId,
        ]);

        $result = $this->service->handle();

        return [
            'code' => 0,
            'message' => 'success',
            'data' => $result,
        ];
    }
}