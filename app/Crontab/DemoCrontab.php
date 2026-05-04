<?php

declare(strict_types=1);

namespace App\Crontab;

use Hyperf\Crontab\Annotation\Crontab;

#[Crontab(
    rule: '*/10 * * * * *',
    name: 'DemoCrontab',
    callback: 'execute',
    memo: '每 10 秒执行一次 Demo 定时任务'
)]
class DemoCrontab
{
    public function execute(): void
    {
        var_dump('=== DemoCrontab execute ===');
        var_dump([
            'time' => date('Y-m-d H:i:s'),
            'message' => '定时任务执行成功',
        ]);
    }
}