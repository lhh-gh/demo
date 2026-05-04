<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class LifecycleDemoMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->logger->info('2. 中间件 before');

        $response = $handler->handle($request);

        $this->logger->info('6. 中间件 after');

        return $response;
    }
}