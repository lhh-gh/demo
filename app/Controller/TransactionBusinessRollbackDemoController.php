<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TransactionBusinessRollbackDemoService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;

#[Controller(prefix: 'transaction-business-rollback')]
class TransactionBusinessRollbackDemoController
{
    public function __construct(
        protected TransactionBusinessRollbackDemoService $service
    ) {
    }

    #[PostMapping('test')]
    public function test(): array
    {
        return $this->service->createOrderAndRollback(1001, 1);
    }
}