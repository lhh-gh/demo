<?php

declare(strict_types=1);

namespace App\Schema\Foo;

use Hyperf\Swagger\Annotation as SA;

/**
 * Foo 列表返回结构 Schema
 */
class FooListSchema
{
    public static function response(): SA\JsonContent
    {
        return new SA\JsonContent(
            properties: [
                new SA\Property(property: 'code', type: 'integer', example: 0),
                new SA\Property(property: 'message', type: 'string', example: 'success'),
                new SA\Property(
                    property: 'data',
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
                    ],
                    type: 'object'
                ),
            ],
            type: 'object'
        );
    }
}