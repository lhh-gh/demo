<?php

declare(strict_types=1);

namespace App\Crontab;

use Hyperf\Crontab\Annotation\Crontab;

#[Crontab(
    rule: '*/30 * * * * *',
    name: 'RetryFailedJobCrontab',
    callback: 'execute',
    memo: '每 30 秒扫描一次失败任务'
)]
class RetryFailedJobCrontab
{
    public function execute(): void
    {
        var_dump('=== RetryFailedJobCrontab execute ===');
        var_dump([
            'time' => date('Y-m-d H:i:s'),
            'message' => '扫描失败任务补偿逻辑（模拟）',
        ]);
    }
}