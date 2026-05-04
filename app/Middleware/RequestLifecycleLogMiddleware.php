<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Context\Context;
use Hyperf\Coroutine\Coroutine;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestLifecycleLogMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected RequestInterface $request
    ) {
    }

    public function process(\Psr\Http\Message\ServerRequestInterface $request, RequestHandlerInterface $handler):
    ResponseInterface
    {
        // 当前请求协程 ID
        $cid = Coroutine::id();

        // 生成请求唯一标识
        $requestId = uniqid('req_', true);

        // 放入协程上下文，供后续 Controller / Service 共用
        Context::set('request_id', $requestId);
        Context::set('request_start_time', microtime(true));

        var_dump('=== Middleware before ===');
        var_dump([
            'cid' => $cid,
            'request_id' => $requestId,
            'method' => $this->request->getMethod(),
            'path' => $this->request->path(),
        ]);

        $response = $handler->handle($request);

        $cost = round((microtime(true) - (float) Context::get('request_start_time', microtime(true))) * 1000, 2);

        var_dump('=== Middleware after ===');
        var_dump([
            'cid' => $cid,
            'request_id' => Context::get('request_id'),
            'cost_ms' => $cost,
        ]);

        return $response;
    }
}