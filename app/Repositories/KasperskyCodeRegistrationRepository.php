<?php

namespace App\Repositories;

use App\Models\KasperskyCodeRegistration;

class KasperskyCodeRegistrationRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new KasperskyCodeRegistration();
        $this->relations = [
            'device:id,name',
            'createdBy:id,name',
            'approvedBy:id,name',
        ];
    }

    public function getStatus($key = null)
    {
        return $this->model->getStatus($key);
    }

    public function getSearchConfig(): array
    {
        return [
            'text' => [],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'device'=>['name'],
                'createdBy'=>['name'],
                'approvedBy'=>['name'],
            ]
        ];
    }
}
