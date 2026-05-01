<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    App\Repository\UserRepositoryInterface::class => App\Repository\UserRepository::class,
    App\Service\UserServiceInterface::class => App\Service\UserService::class,
];

// 这样 Hyperf 容器在遇到 UserServiceInterface 时，就会自动实例化 UserService。
