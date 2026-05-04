<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;

class LifecycleDemoService
{
    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    public function handle(): array
    {
        $this->logger->info('4. 进入 Service');

        return [
            'step' => 'service done',
        ];
    }
}