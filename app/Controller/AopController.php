<?php
declare(strict_types=1);
namespace App\Controller;
use App\Service\AopService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
#[Controller(prefix: 'aop')]
class AopController
{
    public function __construct(
        protected AopService $userService
    ) {
    }

    #[GetMapping('info')]
    public function info(): array
    {
        return $this->userService->getUserInfo(1);
    }
}