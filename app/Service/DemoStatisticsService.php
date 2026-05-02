<?php

declare(strict_types=1);

namespace App\Service;

use Hyperf\DbConnection\Db;

class DemoStatisticsService
{
    /**
     * join 查询：查询用户及其订单信息
     */
    public function joinUsersAndOrders(): array
    {
        return Db::table('demo_users as u')
            ->join('demo_orders as o', 'u.id', '=', 'o.user_id')
            ->select([
                'u.id',
                'u.username',
                'u.email',
                'o.order_no',
                'o.amount',
                'o.status',
            ])
            ->get()
            ->toArray();
    }

    /**
     * count 统计：统计订单总数
     */
    public function countOrders(): int
    {
        return Db::table('demo_orders')->count();
    }

    /**
     * groupBy 统计：按用户统计订单数量
     */
    public function countOrdersGroupByUser(): array
    {
        return Db::table('demo_orders')
            ->select([
                'user_id',
                Db::raw('COUNT(*) as order_count'),
            ])
            ->groupBy('user_id')
            ->get()
            ->toArray();
    }

    /**
     * groupBy + sum：按用户统计订单总金额
     */
    public function sumAmountGroupByUser(): array
    {
        return Db::table('demo_orders')
            ->select([
                'user_id',
                Db::raw('SUM(amount) as total_amount'),
            ])
            ->groupBy('user_id')
            ->get()
            ->toArray();
    }

    /**
     * join + groupBy：统计每个用户的订单数量
     */
    public function userOrderStatistics(): array
    {
        return Db::table('demo_users as u')
            ->leftJoin('demo_orders as o', 'u.id', '=', 'o.user_id')
            ->select([
                'u.id',
                'u.username',
                Db::raw('COUNT(o.id) as order_count'),
                Db::raw('IFNULL(SUM(o.amount), 0) as total_amount'),
            ])
            ->groupBy('u.id', 'u.username')
            ->orderBy('u.id')
            ->get()
            ->toArray();
    }
}