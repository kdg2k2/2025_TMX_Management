<?php

namespace App\Repositories;

use App\Models\UserWarning;

class UserWarningRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new UserWarning();
        $this->relations = [
            'createdBy',
            'user',
        ];
    }

    public function getType($key = null)
    {
        return $this->model->getType($key);
    }

    public function isHasWarning(int $userId, string $warningDate)
    {
        return $this->model->where('user_id', $userId)->where('warning_date', $warningDate)->exists();
    }
}
