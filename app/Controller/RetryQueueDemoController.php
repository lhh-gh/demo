<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\RetryQueueDemoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'queue-retry-demo')]
class RetryQueueDemoController
{
    public function __construct(
        protected RetryQueueDemoService $service
    ) {
    }

    #[GetMapping('push')]
    public function push(): array
    {
        return $this->service->pushRetryJob();
    }
}