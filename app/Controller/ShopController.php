<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\Swagger\Annotation as SA;

#[SA\HyperfServer('http')]
#[Controller]
class ShopController
{
    #[SA\Get(
        path: '/shop/list',
        summary: '列表查询',
        tags: ['Shop']
    )]
    #[SA\Parameter(
        name: 'page',
        description: '页码',
        in: 'query',
        required: false,
        schema: new SA\Schema(type: 'integer', default: 1)
    )]
    #[SA\Parameter(
        name: 'page_size',
        description: '每页数量',
        in: 'query',
        required: false,
        schema: new SA\Schema(type: 'integer', default: 10)
    )]
    #[SA\Response(response: 200, description: '查询成功')]
    public function list(): array
    {
        return [];
    }

    #[SA\Get(
        path: '/shop/detail',
        summary: '详情查询',
        tags: ['Shop']
    )]
    #[SA\Parameter(
        name: 'id',
        description: '主键ID',
        in: 'query',
        required: true,
        schema: new SA\Schema(type: 'integer')
    )]
    #[SA\Response(response: 200, description: '查询成功')]
    public function detail(): array
    {
        return [];
    }

    #[SA\Post(
        path: '/shop/create',
        summary: '新增数据',
        tags: ['Shop']
    )]
    #[SA\RequestBody(
        description: '请求参数',
        content: [
            new SA\MediaType(
                mediaType: 'application/json',
                schema: new SA\Schema(
                    required: ['name'],
                    properties: [
                        new SA\Property(property: 'name', type: 'string', description: '名称', example: '测试名称'),
                        new SA\Property(property: 'status', type: 'integer', description: '状态', example: 1),
                    ]
                ),
            ),
        ],
    )]
    #[SA\Response(response: 200, description: '新增成功')]
    public function create(): array
    {
        return [];
    }

    #[SA\Put(
        path: '/shop/update',
        summary: '更新数据',
        tags: ['Shop']
    )]
    #[SA\RequestBody(
        description: '更新参数',
        content: [
            new SA\MediaType(
                mediaType: 'application/json',
                schema: new SA\Schema(
                    required: ['id', 'name'],
                    properties: [
                        new SA\Property(property: 'id', type: 'integer', description: '主键ID', example: 1),
                        new SA\Property(property: 'name', type: 'string', description: '名称', example: '新名称'),
                    ]
                ),
            ),
        ],
    )]
    #[SA\Response(response: 200, description: '更新成功')]
    public function update(): array
    {
        return [];
    }

    #[SA\Delete(
        path: '/shop/delete',
        summary: '删除数据',
        tags: ['Shop']
    )]
    #[SA\Parameter(
        name: 'id',
        description: '主键ID',
        in: 'query',
        required: true,
        schema: new SA\Schema(type: 'integer')
    )]
    #[SA\Response(response: 200, description: '删除成功')]
    public function delete(): array
    {
        return [];
    }
}
