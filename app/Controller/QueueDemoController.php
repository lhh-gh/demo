<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\QueueDemoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'queue-demo')]
class QueueDemoController
{
    public function __construct(
        protected QueueDemoService $service
    ) {
    }

    #[GetMapping('push')]
    public function push(): array
    {
        return $this->service->createOrderAndPushJob();
    }
}