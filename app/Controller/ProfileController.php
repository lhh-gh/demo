<?php

namespace App\Controller;

use App\Service\ProfileService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
#[Controller(prefix: "profile")]
class ProfileController
{
    #[Inject]
    protected ProfileService $profileService;

    #[Inject]
    protected RequestInterface $request;
    /**
     * 获取会员资料
     */
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
    #[PostMapping(path: "update-nickname")]
    public function updateNickname()
    {
        $id = (int) $this->request->input('id');
        $nickname = (string)$this->request->input('nickname');

        $result = $this->profileService->updateNickname($id, $nickname);

        return [
            'code' => $result ? 0 : 1,
            'message' => $result ? '更新成功' : '更新失败',
        ];
    }
}