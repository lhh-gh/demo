<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\IdempotentQueueDemoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'queue-idempotent-demo')]
class IdempotentQueueDemoController
{
    public function __construct(
        protected IdempotentQueueDemoService $service
    ) {
    }

    #[GetMapping('push')]
    public function push(): array
    {
        return $this->service->pushIdempotentJob();
    }
}