<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DemoUserOrmService;
use App\Service\DemoUserQueryService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'demo-user')]
class DemoUserController
{
    public function __construct(
        protected DemoUserOrmService $ormService,
        protected DemoUserQueryService $queryService
    ) {
    }

    /**
     * ORM 查询全部
     */
    #[GetMapping('orm/all')]
    public function ormAll(): array
    {
        return [
            'type' => 'orm',
            'data' => $this->ormService->all(),
        ];
    }

    /**
     * ORM 查询启用用户
     */
    #[GetMapping('orm/active')]
    public function ormActive(): array
    {
        return [
            'type' => 'orm',
            'data' => $this->ormService->activeUsers(),
        ];
    }

    /**
     * Query Builder 查询全部
     */
    #[GetMapping('query/all')]
    public function queryAll(): array
    {
        return [
            'type' => 'query_builder',
            'data' => $this->queryService->all(),
        ];
    }

    /**
     * Query Builder 查询年龄大于 20 的用户
     */
    #[GetMapping('query/older')]
    public function queryOlder(): array
    {
        return [
            'type' => 'query_builder',
            'data' => $this->queryService->olderThan(20),
        ];
    }
}