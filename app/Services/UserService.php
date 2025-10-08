<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService extends BaseService
{
    public function __construct(
        private UserRepository $userRepository,
        private HandlerUploadFileService $handlerUploadFileService
    ) {}

    public function findByEmail(string $email)
    {
        return $this->userRepository->findByEmail($email);
    }

    public function findByGoogleId(string $googleId)
    {
        return $this->userRepository->findByGoogleId($googleId);
    }

    public function delete(int $id)
    {
        return $this->tryThrow(function () use ($id) {
            $user = $this->userRepository->findById($id);
            if ($user && !empty($user['path']))
                $this->handlerUploadFileService->removeFile($user['path']);
            if ($user && !empty($user['path_signature']))
                $this->handlerUploadFileService->removeFile($user['path_signature']);

            $this->userRepository->delete($id);
        }, true);
    }

    public function changePassword($password)
    {
        return $this->tryThrow(function () use ($password) {
            $user = $this->userRepository->findById($this->getGuard()->user()->id);
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
