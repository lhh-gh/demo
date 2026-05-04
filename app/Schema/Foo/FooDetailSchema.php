<?php

declare(strict_types=1);

namespace App\Schema\Foo;

use Hyperf\Swagger\Annotation as SA;

/**
 * Foo 详情返回结构 Schema
 */
class FooDetailSchema
{
    public static function response(): SA\JsonContent
    {
        return new SA\JsonContent(
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
        );
    }
}