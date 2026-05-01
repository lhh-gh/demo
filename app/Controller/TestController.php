<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Swagger\Annotation as SA;

#[SA\HyperfServer('http')]
#[Controller(prefix: 'test')]
class TestController
{
    #[SA\Post(path: '/test', summary: 'POST 表单示例', tags: ['Api/Test'])]
    #[SA\RequestBody(
        description: '请求参数',
        content: [
            new SA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new SA\Schema(
                    required: ['username', 'age'],
                    properties: [
                        new SA\Property(property: 'username', description: '用户名字段描述', type: 'string'),
                        new SA\Property(property: 'age', description: '年龄字段描述', type: 'string'),
                        new SA\Property(property: 'city', description: '城市字段描述', type: 'string'),
                    ]
                ),
            ),
        ],
    )]
    #[SA\Response(response: 200, description: '返回值的描述')]
    public function test()
    {

    }
}