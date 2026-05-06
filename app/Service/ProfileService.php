<?php

namespace App\Service;

use App\Repository\MemberRepository;
use Hyperf\Di\Annotation\Inject;

class ProfileService
{
    #[Inject]
    protected MemberRepository $memberRepository;

    /**
     * 获取会员资料
     */
    public function getDetail(int $id): array
    {
        if ($id <= 0) {
            return [
                'code' => 1,
                'message' => '参数错误',
            ];
        }

        $member = $this->memberRepository->findByIdWithCache($id);

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
    public function updateNickname(int $id, string $nickname): bool
    {
        if ($id <= 0 || $nickname === '') {
            return false;
        }

        return $this->memberRepository->updateByIdAndClearCache($id, [
            'nickname' => $nickname,
        ]);
    }
}