<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\HttpServer\Contract\ResponseInterface as ResponseContract;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CheckTokenMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected ContainerInterface $container,
        protected ResponseContract $response
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine('token');

        if ($token !== 'demo-token') {
            return $this->response->json([
                'code' => 401,
                'message' => 'token 无效',
                'data' => null,
            ]);
        }

        return $handler->handle($request);
    }
}