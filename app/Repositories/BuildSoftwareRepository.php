<?php

namespace App\Repositories;

use App\Models\BuildSoftware;

class BuildSoftwareRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new BuildSoftware();
        $this->relations = [
            'contract',
            'createdBy',
            'verifyBy',
            'businessAnalysts.user',
            'members.user',
        ];
    }

    public function getDevelopmentCase($key = null)
    {
        return $this->model->getDevelopmentCase($key);
    }

    public function getState($key = null)
    {
        return $this->model->getState($key);
    }

    public function getStatus($key = null)
    {
        return $this->model->getStatus($key);
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['development_case']))
            $query->where('development_case', $request['development_case']);
        if (isset($request['state']))
            $query->where('state', $request['state']);
        if (isset($request['status']))
            $query->where('status', $request['status']);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
                'description',
                'rejection_reason',
            ],
            'date' => [
                'deadline',
                'start_date',
            ],
            'datetime' => [
                'rejected_at',
                'accepted_at',
                'completed_at',
            ],
            'relations' => [
                'createdBy' => ['name'],
                'verifyBy' => ['name'],
                'contract' => ['name'],
            ]
        ];
    }
}
