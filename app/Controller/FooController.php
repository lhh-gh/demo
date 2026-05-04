<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\FooService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\Swagger\Annotation as SA;

#[SA\HyperfServer('http')]
#[Controller]
class FooController
{
    public function __construct(
        protected FooService $service
    ) {
    }

    #[SA\Get(path: '/foo/list', summary: '文章列表', tags: ['Foo 示例'])]
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
    #[SA\Response(
        response: 200,
        description: '查询成功',
        content: new SA\JsonContent(
            properties: [
                new SA\Property(property: 'code', type: 'integer', example: 0),
                new SA\Property(property: 'message', type: 'string', example: 'success'),
                new SA\Property(
                    property: 'data',
                    type: 'object',
                    properties: [
                        new SA\Property(
                            property: 'list',
                            type: 'array',
                            items: new SA\Items(
                                type: 'object',
                                properties: [
                                    new SA\Property(property: 'id', type: 'integer', example: 1),
                                    new SA\Property(property: 'title', type: 'string', example: 'Hyperf 分层实践'),
                                    new SA\Property(property: 'author_name', type: 'string', example: '张三'),
                                    new SA\Property(property: 'status', type: 'integer', example: 1),
                                ]
                            )
                        ),
                        new SA\Property(
                            property: 'pagination',
                            type: 'object',
                            properties: [
                                new SA\Property(property: 'total', type: 'integer', example: 100),
                                new SA\Property(property: 'page', type: 'integer', example: 1),
                                new SA\Property(property: 'page_size', type: 'integer', example: 10),
                            ]
                        ),
                    ]
                ),
            ],
            type: 'object'
        )
    )]
    public function list(): array
    {
        return [
            'code' => 0,
            'message' => 'success',
            'data' => $this->service->getList(1, 10),
        ];
    }

    #[SA\Get(path: '/foo/detail', summary: '文章详情', tags: ['Foo 示例'])]
    #[SA\Parameter(
        name: 'id',
        description: '文章ID',
        in: 'query',
        required: true,
        schema: new SA\Schema(type: 'integer')
    )]
    #[SA\Response(
        response: 200,
        description: '查询成功',
        content: new SA\JsonContent(
            properties: [
                new SA\Property(property: 'code', type: 'integer', example: 0),
                new SA\Property(property: 'message', type: 'string', example: 'success'),
                new SA\Property(
                    property: 'data',
                    type: 'object',
                    properties: [
                        new SA\Property(property: 'id', type: 'integer', example: 1),
                        new SA\Property(property: 'title', type: 'string', example: 'Hyperf 分层实践'),
                        new SA\Property(property: 'content', type: 'string', example: '正文内容'),
                        new SA\Property(property: 'author_name', type: 'string', example: '张三'),
                        new SA\Property(property: 'status', type: 'integer', example: 1),
                    ]
                ),
            ],
            type: 'object'
        )
    )]
    public function detail(): array
    {
        return [
            'code' => 0,
            'message' => 'success',
            'data' => $this->service->getDetail(1),
        ];
    }

    #[SA\Post(path: '/foo/create', summary: '创建文章', tags: ['Foo 示例'])]
    #[SA\RequestBody(
        description: '创建文章参数',
        content: [
            new SA\MediaType(
                mediaType: 'application/json',
                schema: new SA\Schema(
                    required: ['title', 'content', 'author_name'],
                    properties: [
                        new SA\Property(property: 'title', type: 'string', example: 'Hyperf 分层实践'),
                        new SA\Property(property: 'content', type: 'string', example: '正文内容'),
                        new SA\Property(property: 'author_name', type: 'string', example: '张三'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    #[SA\Response(
        response: 200,
        description: '创建成功',
        content: new SA\JsonContent(
            properties: [
                new SA\Property(property: 'code', type: 'integer', example: 0),
                new SA\Property(property: 'message', type: 'string', example: '创建成功'),
                new SA\Property(
                    property: 'data',
                    type: 'object',
                    properties: [
                        new SA\Property(property: 'id', type: 'integer', example: 1),
                        new SA\Property(property: 'title', type: 'string', example: 'Hyperf 分层实践'),
                        new SA\Property(property: 'content', type: 'string', example: '正文内容'),
                        new SA\Property(property: 'author_name', type: 'string', example: '张三'),
                        new SA\Property(property: 'status', type: 'integer', example: 1),
                    ]
                ),
            ],
            type: 'object'
        )
    )]
    public function create(): array
    {
        return [
            'code' => 0,
            'message' => '创建成功',
            'data' => $this->service->create('Hyperf 分层实践', '正文内容', '张三'),
        ];
    }
}