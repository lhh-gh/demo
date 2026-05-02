<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\Redis\Redis;

#[Controller(prefix: 'redis')]
class RedisController
{
    #[Inject]
    protected Redis $redis;

    #[GetMapping('demo')]
    public function demo(): array
    {
        $this->redis->set('site', 'hyperf', 60);

        return [
            'value' => $this->redis->get('site'),
        ];
    }
}