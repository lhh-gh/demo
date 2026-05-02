<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DemoTransactionService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'demo-transaction')]
class DemoTransactionController
{
    public function __construct(
        protected DemoTransactionService $transactionService
    ) {
    }

    /**
     * 事务成功示例
     */
    #[GetMapping('success')]
    public function success(): array
    {
        return $this->transactionService->createTwoUsers();
    }

    /**
     * 事务回滚示例
     */
    #[GetMapping('rollback')]
    public function rollback(): array
    {
        return $this->transactionService->rollbackDemo();
    }
}