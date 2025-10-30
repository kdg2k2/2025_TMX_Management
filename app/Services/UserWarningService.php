<?php

namespace App\Services;

use App\Repositories\UserWarningRepository;
use Exception;

class UserWarningService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(UserWarningRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        $array['type'] = $this->repository->getType($array['type']);
        return $array;
    }

    public function beforeStore(array $request)
    {
        if ($this->repository->isHasWarning($request['user_id'], $request['warning_date']))
            throw new Exception('Người đã này đã bị cảnh cáo trong ngày rồi!');

        if ($request['type'] == 'work_schedule') {
            $request['detail'] = json_encode([
                'work_schedule_id' => $request['work_schedule_id'],
            ]);
            unset($request['work_schedule_id']);
        }

        return $request;
    }
}
