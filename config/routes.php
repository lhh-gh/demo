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
use Hyperf\HttpServer\Router\Router;

// 此处代码示例为每个示例都提供了三种不同的绑定定义方式，实际配置时仅可采用一种且仅定义一次相同的路由

// 设置一个 GET 请求的路由，绑定访问地址 '/get' 到 App\Controller\IndexController 的 get 方法
Router::get('/get', 'App\Controller\IndexController::get');
// Router::get('/get', 'App\Controller\IndexController@get');
// Router::get('/get', [\App\Controller\IndexController::class, 'get']);

// 设置一个 POST 请求的路由，绑定访问地址 '/post' 到 App\Controller\IndexController 的 post 方法
Router::post('/post', 'App\Controller\IndexController::post');
// Router::post('/post', 'App\Controller\IndexController@post');
// Router::post('/post', [\App\Controller\IndexController::class, 'post']);
