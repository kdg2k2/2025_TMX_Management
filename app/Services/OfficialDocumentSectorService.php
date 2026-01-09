<?php

namespace App\Services;

use App\Repositories\OfficialDocumentSectorRepository;

class OfficialDocumentSectorService extends BaseService
{
    public function __construct(
        private UserService $userService
    ) {
        $this->repository = app(OfficialDocumentSectorRepository::class);
    }

    protected function extractBefore(array $request)
    {
        $users = $request['users'];
        unset($request['users']);
        return [
            'request' => $request,
            'users' => $users,
        ];
    }

    protected function handleExtractAfter(array $extract, $data)
    {
        $data->users()->sync($extract['users']);
    }

    public function getBaseDataForCEView(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->repository->findById($id);
        $res['users'] = $this->userService->list([
            'load_relations' => false,
            'columns' => [
                'id',
                'name',
            ]
        ]);
        return $res;
    }
}
