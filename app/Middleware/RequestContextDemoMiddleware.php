<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Context\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class RequestContextDemoMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 模拟当前请求唯一 ID
        $requestId = uniqid('req_', true);

        // 模拟当前登录用户 ID
        $currentUserId = 1001;

        // 写入协程上下文
        Context::set('request_id', $requestId);
        Context::set('current_user_id', $currentUserId);

        $this->logger->info('Context middleware before', [
            'request_id' => $requestId,
            'current_user_id' => $currentUserId,
        ]);

        $response = $handler->handle($request);

        $this->logger->info('Context middleware after', [
            'request_id' => Context::get('request_id'),
            'current_user_id' => Context::get('current_user_id'),
        ]);

        return $response;
    }
}