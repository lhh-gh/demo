<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\Swagger\Annotation as SA;

#[SA\HyperfServer('http')]
#[Controller]
class UserController
{
    #[SA\Get(
        path: '/user/list',
        summary: '用户列表',
        tags: ['用户管理']
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
    #[SA\Parameter(
        name: 'keyword',
        description: '搜索关键词',
        in: 'query',
        required: false,
        schema: new SA\Schema(type: 'string')
    )]
    #[SA\Response(
        response: 200,
        description: '查询成功',
        content: new SA\JsonContent(
            properties: [
                new SA\Property(property: 'code', type: 'integer', example: 200),
                new SA\Property(property: 'message', type: 'string', example: 'success'),
                new SA\Property(
                    property: 'data',
                    properties: [
                        new SA\Property(
                            property: 'list',
                            type: 'array',
                            items: new SA\Items(
                                properties: [
                                    new SA\Property(property: 'id', type: 'integer', example: 1),
                                    new SA\Property(property: 'username', type: 'string', example: 'zhangsan'),
                                    new SA\Property(property: 'email', type: 'string', example:
                                        'zhangsan@example.com'),
                                ],
                                type: 'object'
                            )
                        ),
                        new SA\Property(
                            property: 'pagination',
                            properties: [
                                new SA\Property(property: 'total', type: 'integer', example: 100),
                                new SA\Property(property: 'page', type: 'integer', example: 1),
                                new SA\Property(property: 'page_size', type: 'integer', example: 10),
                            ],
                            type: 'object'
                        ),
                    ],
                    type: 'object'
                ),
            ],
            type: 'object'
        )
    )]
    public function list(): array
    {
        return [
            'code' => 200,
            'message' => 'success',
            'data' => [
                'list' => [
                    [
                        'id' => 1,
                        'username' => 'zhangsan',
                        'email' => 'zhangsan@example.com',
                    ],
                    [
                        'id' => 2,
                        'username' => 'lisi',
                        'email' => 'lisi@example.com',
                    ],
                ],
                'pagination' => [
                    'total' => 2,
                    'page' => 1,
                    'page_size' => 10,
                ],
            ],
        ];
    }

    #[SA\Get(
        path: '/user/detail',
        summary: '用户详情',
        tags: ['用户管理']
    )]
    #[SA\Parameter(
        name: 'id',
        description: '用户ID',
        in: 'query',
        required: true,
        schema: new SA\Schema(type: 'integer')
    )]
    #[SA\Response(
        response: 200,
        description: '查询成功',
        content: new SA\JsonContent(
            properties: [
                new SA\Property(property: 'code', type: 'integer', example: 200),
                new SA\Property(property: 'message', type: 'string', example: 'success'),
                new SA\Property(
                    property: 'data',
                    properties: [
                        new SA\Property(property: 'id', type: 'integer', example: 1),
                        new SA\Property(property: 'username', type: 'string', example: 'zhangsan'),
                        new SA\Property(property: 'email', type: 'string', example: 'zhangsan@example.com'),
                    ],
                    type: 'object'
                ),
            ],
            type: 'object'
        )
    )]
    public function detail(): array
    {
        return [
            'code' => 200,
            'message' => 'success',
            'data' => [
                'id' => 1,
                'username' => 'zhangsan',
                'email' => 'zhangsan@example.com',
            ],
        ];
    }

    #[SA\Post(
        path: '/user/create',
        summary: '创建用户',
        tags: ['用户管理']
    )]
    #[SA\RequestBody(
        description: '用户信息',
        content: [
            new SA\MediaType(
                mediaType: 'application/json',
                schema: new SA\Schema(
                    required: ['username', 'email'],
                    properties: [
                        new SA\Property(
                            property: 'username',
                            type: 'string',
                            description: '用户名',
                            example: 'wangwu'
                        ),
                        new SA\Property(
                            property: 'email',
                            type: 'string',
                            description: '邮箱',
                            example: 'wangwu@example.com'
                        ),
                    ]
                ),
            ),
        ],
    )]
    #[SA\Response(
        response: 200,
        description: '用户创建成功',
        content: new SA\JsonContent(
            properties: [
                new SA\Property(property: 'code', type: 'integer', example: 200),
                new SA\Property(property: 'message', type: 'string', example: '用户创建成功'),
                new SA\Property(
                    property: 'data',
                    properties: [
                        new SA\Property(property: 'id', type: 'integer', example: 3),
                        new SA\Property(property: 'username', type: 'string', example: 'wangwu'),
                        new SA\Property(property: 'email', type: 'string', example: 'wangwu@example.com'),
                    ],
                    type: 'object'
                ),
            ],
            type: 'object'
        )
    )]
    public function create(): array
    {
        return [
            'code' => 200,
            'message' => '用户创建成功',
            'data' => [
                'id' => 3,
                'username' => 'wangwu',
                'email' => 'wangwu@example.com',
            ],
        ];
    }

    #[SA\Put(
        path: '/user/update',
        summary: '更新用户',
        tags: ['用户管理']
    )]
    #[SA\RequestBody(
        description: '更新用户信息',
        content: [
            new SA\MediaType(
                mediaType: 'application/json',
                schema: new SA\Schema(
                    required: ['id', 'username', 'email'],
                    properties: [
                        new SA\Property(property: 'id', type: 'integer', description: '用户ID', example: 1),
                        new SA\Property(property: 'username', type: 'string', description: '用户名', example:
                            'zhangsan_new'),
                        new SA\Property(property: 'email', type: 'string', description: '邮箱', example:
                            'new@example.com'),
                    ]
                ),
            ),
        ],
    )]
    #[SA\Response(
        response: 200,
        description: '用户更新成功',
        content: new SA\JsonContent(
            properties: [
                new SA\Property(property: 'code', type: 'integer', example: 200),
                new SA\Property(property: 'message', type: 'string', example: '用户更新成功'),
                new SA\Property(
                    property: 'data',
                    properties: [
                        new SA\Property(property: 'id', type: 'integer', example: 1),
                        new SA\Property(property: 'username', type: 'string', example: 'zhangsan_new'),
                        new SA\Property(property: 'email', type: 'string', example: 'new@example.com'),
                    ],
                    type: 'object'
                ),
            ],
            type: 'object'
        )
    )]
    public function update(): array
    {
        return [
            'code' => 200,
            'message' => '用户更新成功',
            'data' => [
                'id' => 1,
                'username' => 'zhangsan_new',
                'email' => 'new@example.com',
            ],
        ];
    }

    #[SA\Delete(
        path: '/user/delete',
        summary: '删除用户',
        tags: ['用户管理']
    )]
    #[SA\Parameter(
        name: 'id',
        description: '用户ID',
        in: 'query',
        required: true,
        schema: new SA\Schema(type: 'integer')
    )]
    #[SA\Response(
        response: 200,
        description: '用户删除成功',
        content: new SA\JsonContent(
            properties: [
                new SA\Property(property: 'code', type: 'integer', example: 200),
                new SA\Property(property: 'message', type: 'string', example: '用户删除成功'),
                new SA\Property(property: 'data', type: 'null', example: null),
            ],
            type: 'object'
        )
    )]
    public function delete(): array
    {
        return [
            'code' => 200,
            'message' => '用户删除成功',
            'data' => null,
        ];
    }
}