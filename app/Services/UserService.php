<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(UserRepository::class);
    }

    protected function beforeDelete(int $id)
    {
        $user = $this->repository->findById($id);
        $this->handlerUploadFileService->removeFiles([$user['path'], $user['path_signature']]);
    }

    public function changePassword($password)
    {
        return $this->tryThrow(function () use ($password) {
            $user = $this->repository->findById($this->getGuard()->user()->id);
            $user->password = bcrypt($password);
            $user->save();
            return true;
        });
    }

    public function incrementJwtVersion(int $userId = null)
    {
        return $this->tryThrow(function () use ($userId) {
            if ($userId) {
                $user = $this->repository->findById($userId);
                if ($user) {
                    $user->increment('jwt_version');
                    app(AuthService::class)->logout();
                }
            } else {
                $this->repository->model->increment('jwt_version');
            }
        }, true);
    }
}
