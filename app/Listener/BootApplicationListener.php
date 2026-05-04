<?php

declare(strict_types=1);

namespace App\Listener;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Psr\Log\LoggerInterface;

#[Listener]
class BootApplicationListener implements ListenerInterface
{
    public function __construct(
        protected LoggerInterface $logger
    ) {
    }

    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event): void
    {
        $this->logger->info('应用启动阶段：BootApplication 事件触发');
    }
}