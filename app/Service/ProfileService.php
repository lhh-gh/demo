<?php

namespace App\Service;

use App\Repository\MemberRepository;
use Hyperf\Di\Annotation\Inject;

class ProfileService
{
    /**
     * 注入仓储层
     */
    #[Inject]
    protected MemberRepository $memberRepository;

    /**
     * 获取会员详情
     */
    public function getDetail(int $id): array
    {
        if ($id <= 0) {
            return [
                'code' => 1,
                'message' => '会员ID不合法',
            ];
        }

        $member = $this->memberRepository->findByIdWithCacheProtect($id);

        if (! $member) {
            return [
                'code' => 404,
                'message' => '会员不存在',
            ];
        }

        return [
            'code' => 0,
            'message' => 'success',
            'data' => $member,
        ];
    }

    /**
     * 修改会员昵称
     */
    public function updateNickname(int $id, string $nickname): array
    {
        if ($id <= 0 || $nickname === '') {
            return [
                'code' => 1,
                'message' => '参数不合法',
            ];
        }

        $result = $this->memberRepository->updateNicknameWithDelayedDoubleDelete($id, $nickname);

        return [
            'code' => $result ? 0 : 1,
            'message' => $result ? '更新成功' : '更新失败',
        ];
    }
}