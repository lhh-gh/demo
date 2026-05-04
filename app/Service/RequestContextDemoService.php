<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\Context\Context;
use Psr\Log\LoggerInterface;

class RequestContextDemoService
{
    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    public function handle(): array
    {
        $requestId = Context::get('request_id');
        $currentUserId = Context::get('current_user_id');

        $this->logger->info('Service read context', [
            'request_id' => $requestId,
            'current_user_id' => $currentUserId,
        ]);

        return [
            'request_id' => $requestId,
            'current_user_id' => $currentUserId,
            'message' => 'Context read success',
        ];
    }
}