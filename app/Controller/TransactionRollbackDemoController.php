<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TransactionRollbackDemoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;

#[Controller(prefix: 'transaction-rollback')]
class TransactionRollbackDemoController
{
    public function __construct(
        protected TransactionRollbackDemoService $service
    ) {
    }

    /**
     * 中途报错自动回滚 Demo
     */
    #[PostMapping('test')]
    public function test(): array
    {
        return $this->service->createOrderAndRollback(1001, 1);
    }
}