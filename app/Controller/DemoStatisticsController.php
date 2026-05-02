<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DemoStatisticsService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'demo-stat')]
class DemoStatisticsController
{
    public function __construct(
        protected DemoStatisticsService $statisticsService
    ) {
    }

    /**
     * join 查询
     */
    #[GetMapping('join')]
    public function join(): array
    {
        return [
            'type' => 'join',
            'data' => $this->statisticsService->joinUsersAndOrders(),
        ];
    }

    /**
     * count 统计
     */
    #[GetMapping('count')]
    public function count(): array
    {
        return [
            'type' => 'count',
            'data' => [
                'order_total' => $this->statisticsService->countOrders(),
            ],
        ];
    }

    /**
     * groupBy 统计订单数量
     */
    #[GetMapping('group-count')]
    public function groupCount(): array
    {
        return [
            'type' => 'groupBy count',
            'data' => $this->statisticsService->countOrdersGroupByUser(),
        ];
    }

    /**
     * groupBy 统计总金额
     */
    #[GetMapping('group-sum')]
    public function groupSum(): array
    {
        return [
            'type' => 'groupBy sum',
            'data' => $this->statisticsService->sumAmountGroupByUser(),
        ];
    }

    /**
     * join + groupBy 综合统计
     */
    #[GetMapping('user-order-stat')]
    public function userOrderStat(): array
    {
        return [
            'type' => 'join + groupBy',
            'data' => $this->statisticsService->userOrderStatistics(),
        ];
    }
}