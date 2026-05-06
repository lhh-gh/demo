<?php

namespace App\Controller;

use App\Service\ProfileService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\RequestInterface;

#[Controller(prefix: 'profile')]
class ProfileController
{
    /**
     * 注入 Service
     */
    #[Inject]
    protected ProfileService $profileService;

    /**
     * 注入请求对象
     */
    #[Inject]
    protected RequestInterface $request;

    /**
     * 获取会员详情
     */
    #[GetMapping(path: 'detail')]
    public function detail()
    {
        $id = (int) $this->request->input('id', 0);

        return $this->profileService->getDetail($id);
    }

    /**
     * 修改会员昵称
     */
    #[PostMapping(path: 'update-nickname')]
    public function updateNickname()
    {
        $id = (int) $this->request->input('id', 0);
        $nickname = (string) $this->request->input('nickname', '');

        return $this->profileService->updateNickname($id, $nickname);
    }
}