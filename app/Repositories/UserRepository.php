<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new User();
        $this->relations = [
            'role',
            'department.manager',
            'position',
            'jobTitle',
            'subEmails',
        ];
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['department_id']))
            $query->where('department_id', $request['department_id']);
        if (isset($request['position_id']))
            $query->where('position_id', $request['position_id']);
        if (isset($request['job_title_id']))
            $query->where('job_title_id', $request['job_title_id']);
        if (isset($request['role_id']))
            $query->where('role_id', $request['role_id']);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
                'email',
                'citizen_identification_number',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'role' => ['name'],
                'department' => ['name'],
                'position' => ['name'],
                'jobTitle' => ['name'],
            ]
        ];
    }
}
