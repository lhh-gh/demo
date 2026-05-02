<?php

declare(strict_types=1);

namespace App\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Idempotent
{
    public function __construct(
        /**
         * 幂等 key 前缀
         */
        public string $prefix = 'idempotent',

        /**
         * 幂等锁有效时间，单位秒
         */
        public int $ttl = 5
    ) {
    }
}