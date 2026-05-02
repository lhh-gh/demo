<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DemoProductService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;

#[Controller(prefix: 'demo-product')]
class DemoProductController
{
    public function __construct(
        protected DemoProductService $service
    ) {
    }

    /**
     * 商品列表
     */
    #[GetMapping('list')]
    public function list(): array
    {
        return [
            'code' => 0,
            'message' => 'success',
            'data' => $this->service->getList(),
        ];
    }

    /**
     * 商品详情（带缓存）
     */
    #[GetMapping('detail')]
    public function detail(): array
    {
        return [
            'code' => 0,
            'message' => 'success',
            'data' => $this->service->getDetail(1),
        ];
    }

    /**
     * 新增商品
     */
    #[PostMapping('create')]
    public function create(): array
    {
        return [
            'code' => 0,
            'message' => '新增成功',
            'data' => $this->service->create('MacBook Pro', 15999, 20, 1),
        ];
    }

    /**
     * 更新商品
     */
    #[PostMapping('update')]
    public function update(): array
    {
        $result = $this->service->update(1, 'iPhone 16 Pro', 7999, 90, 1);

        return [
            'code' => 0,
            'message' => $result ? '更新成功' : '更新失败',
            'data' => null,
        ];
    }

    /**
     * 删除商品
     */
    #[PostMapping('delete')]
    public function delete(): array
    {
        $result = $this->service->delete(1);

        return [
            'code' => 0,
            'message' => $result ? '删除成功' : '删除失败',
            'data' => null,
        ];
    }
}