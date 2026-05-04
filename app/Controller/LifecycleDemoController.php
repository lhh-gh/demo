<?php

declare(strict_types=1);

namespace App\Controller;

use App\Middleware\LifecycleDemoMiddleware;
use App\Service\LifecycleDemoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Psr\Log\LoggerInterface;

#[Controller(prefix: 'lifecycle')]
#[Middleware(LifecycleDemoMiddleware::class)]
class LifecycleDemoController
{
    public function __construct(
        protected LoggerInterface $logger,
        protected LifecycleDemoService $service
    ) {
    }

    #[GetMapping('test')]
    public function test(): array
    {
        $this->logger->info('1. 进入 Controller');

        $result = $this->service->handle();

        $this->logger->info('5. Controller 准备返回响应');

        return [
            'code' => 0,
            'message' => 'success',
            'data' => $result,
        ];
    }
}