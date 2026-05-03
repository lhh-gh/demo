<?php

declare(strict_types=1);

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Demo API',
    description: 'Hyperf Swagger Demo'
)]
class OpenApi
{
}