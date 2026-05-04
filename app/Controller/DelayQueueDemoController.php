<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DelayQueueDemoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'queue-delay-demo')]
class DelayQueueDemoController
{
    public function __construct(
        protected DelayQueueDemoService $service
    ) {
    }

    #[GetMapping('push')]
    public function push(): array
    {
        return $this->service->pushDelayJob();
    }
}