<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Context\Context;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Coroutine\Coroutine;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestLifecycleDemoMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected ContainerInterface $container,
        protected RequestInterface $request
    ) {
    }

    public function process(\Psr\Http\Message\ServerRequestInterface $request, RequestHandlerInterface $handler):
    ResponseInterface
    {
        // 当前协程 ID
        $cid = Coroutine::id();

        // 模拟 request_id
        $requestId = uniqid('req_', true);

        // 放入协程上下文
        Context::set('request_id', $requestId);
        Context::set('middleware_start_time', microtime(true));

        var_dump('Middleware before');
        var_dump([
            'cid' => $cid,
            'request_id' => $requestId,
            'path' => $this->request->path(),
        ]);

        $response = $handler->handle($request);

        $cost = round((microtime(true) - Context::get('middleware_start_time')) * 1000, 2);

        var_dump('Middleware after');
        var_dump([
            'cid' => $cid,
            'request_id' => Context::get('request_id'),
            'cost_ms' => $cost,
        ]);

        return $response;
    }
}