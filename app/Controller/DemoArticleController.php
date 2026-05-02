<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DemoArticleService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;

#[Controller(prefix: 'articles')]
class DemoArticleController
{
    public function __construct(
        protected DemoArticleService $service
    ) {
    }

    /**
     * 获取文章列表
     * GET /articles
     */
    #[GetMapping('')]
    public function index(): array
    {
        return [
            'code' => 0,
            'message' => 'success',
            'data' => $this->service->getList(),
        ];
    }

    /**
     * 获取文章详情
     * GET /articles/{id}
     */
    #[GetMapping('{id:\d+}')]
    public function show(int $id): array
    {
        return [
            'code' => 0,
            'message' => 'success',
            'data' => $this->service->getDetail($id),
        ];
    }

    /**
     * 创建文章
     * POST /articles
     */
    #[PostMapping('')]
    public function store(): array
    {
        return [
            'code' => 0,
            'message' => '新增成功',
            'data' => $this->service->create(
                '新文章标题',
                '这里是文章内容',
                '王五',
                1
            ),
        ];
    }

    /**
     * 更新文章
     * PUT /articles/{id}
     */
    #[PutMapping('{id:\d+}')]
    public function update(int $id): array
    {
        $result = $this->service->update(
            $id,
            '修改后的标题',
            '修改后的内容',
            '张三',
            1
        );

        return [
            'code' => 0,
            'message' => $result ? '更新成功' : '更新失败',
            'data' => null,
        ];
    }

    /**
     * 删除文章
     * DELETE /articles/{id}
     */
    #[DeleteMapping('{id:\d+}')]
    public function destroy(int $id): array
    {
        $result = $this->service->delete($id);

        return [
            'code' => 0,
            'message' => $result ? '删除成功' : '删除失败',
            'data' => null,
        ];
    }
}