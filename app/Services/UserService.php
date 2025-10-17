<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService,
        private RoleService $roleService,
        private PositionService $positionService,
        private JobTitleService $jobTitleService,
        private DepartmentService $departmentService
    ) {
        $this->repository = app(UserRepository::class);
    }

    public function baseDataForLCEView(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->repository->findById($id);

        $res['roles'] = $this->roleService->list([
            'load_relations' => false,
        ]);
        $res['positions'] = $this->positionService->list([
            'load_relations' => false,
        ]);
        $res['jobTitles'] = $this->jobTitleService->list([
            'load_relations' => false,
        ]);
        $res['departments'] = $this->departmentService->list([
            'load_relations' => false,
        ]);

        return $res;
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

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractFields($request);
            $data = $this->repository->store($request);
            $this->handleFile($data, $extracted);
        }, true);
    }

    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractFields($request);
            $data = $this->repository->update($request);
            $this->handleFile($data, $extracted, true);
        }, true);
    }

    private function extractFields(array &$request): array
    {
        $fields = [
            'path',
            'path_signature',
        ];

        $extracted = [];
        foreach ($fields as $field) {
            $extracted[$field] = $request[$field] ?? null;
            unset($request[$field]);
        }

        return $extracted;
    }

    private function handleFile(User $data, array $extracted, bool $isUpdate = false)
    {
        $fields = [
            'path',
            'path_signature',
        ];
        foreach ($fields as $field) {
            if ($extracted[$field]) {
                $oldFile = $isUpdate ? $data[$field] : null;
                $data[$field] = $this->handlerUploadFileService->storeAndRemoveOld($extracted[$field], 'user', $field, $oldFile);
                $data->save();
            }
        }
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['path']))
            $array['path'] = $this->getAssetImage($array['path']);
        if (isset($array['path_signature']))
            $array['path_signature'] = $this->getAssetUrl($array['path_signature']);
        return $array;
    }

    protected function afterDelete($entity)
    {
        $this->handlerUploadFileService->removeFiles([$entity['path'], $entity['path_signature']]);
    }
}
