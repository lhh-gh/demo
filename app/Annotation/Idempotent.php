<?php

declare(strict_types=1);

namespace App\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_METHOD)]
class Idempotent extends AbstractAnnotation
{
    public function __construct(
        public string $prefix = 'idempotent',
        public int $ttl = 5
    ) {
    }
}