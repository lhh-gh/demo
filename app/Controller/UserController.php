<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\Code;
use App\DTO\UserCreateDTO;
use App\DTO\UserQueryDTO;
use App\DTO\UserUpdateDTO;
use App\Request\UserIndexRequest;
use App\Request\UserStoreRequest;
use App\Request\UserUpdateRequest;
use App\Service\UserServiceInterface;
use App\Support\ApiResponse;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;

#[Controller(prefix: 'users')]
class UserController
{
    use ApiResponse;

    #[Inject]
    protected UserServiceInterface $userService;

    #[GetMapping('')]
    public function index(UserIndexRequest $request): array
    {
        $dto = UserQueryDTO::fromArray($request->validated());

        return $this->success($this->userService->list($dto));
    }

    #[GetMapping('{id:\d+}')]
    public function show(int $id): array
    {
        $user = $this->userService->show($id);

        if (!$user) {
            return $this->error('用户不存在', Code::NOT_FOUND);
        }

        return $this->success($user);
    }

    #[PostMapping('')]
    public function store(UserStoreRequest $request): array
    {
        $dto = UserCreateDTO::fromArray($request->validated());

        return $this->success(
            $this->userService->create($dto),
            '新增成功'
        );
    }

    #[PutMapping('{id:\d+}')]
    public function update(int $id, UserUpdateRequest $request): array
    {
        $dto = UserUpdateDTO::fromArray($request->validated());

        $user = $this->userService->update($id, $dto);

        if (!$user) {
            return $this->error('用户不存在', Code::NOT_FOUND);
        }

        return $this->success($user, '修改成功');
    }

    #[DeleteMapping('{id:\d+}')]
    public function delete(int $id): array
    {
        $result = $this->userService->delete($id);

        if (!$result) {
            return $this->error('用户不存在', Code::NOT_FOUND);
        }

        return $this->success(null, '删除成功');
    }
}