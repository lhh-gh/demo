<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\BadSingletonDemoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Contract\RequestInterface;

#[Controller(prefix: 'bad-singleton')]
class BadSingletonDemoController
{
    public function __construct(
        protected BadSingletonDemoService $service,
        protected RequestInterface $request
    ) {
    }

    #[GetMapping('set')]
    public function set(): array
    {
        $userId = (int) $this->request->input('user_id', 0);

        $this->service->setCurrentUserId($userId);

        return [
            'code' => 0,
            'message' => 'set success',
            'data' => [
                'set_user_id' => $userId,
                'service_current_user_id' => $this->service->getCurrentUserId(),
            ],
        ];
    }

    #[GetMapping('get')]
    public function get(): array
    {
        return [
            'code' => 0,
            'message' => 'get success',
            'data' => [
                'service_current_user_id' => $this->service->getCurrentUserId(),
            ],
        ];
    }
}