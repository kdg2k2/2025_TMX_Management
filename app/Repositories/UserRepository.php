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
        if (isset($request['is_banned']))
            $query->where('is_banned', $request['is_banned']);
        if (isset($request['is_retired']))
            $query->where('is_retired', $request['is_retired']);
        if (isset($request['is_salary_counted']))
            $query->where('is_salary_counted', $request['is_salary_counted']);
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

    protected function customSort($query, array $request)
    {
        return $query
            ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
            ->leftJoin('job_titles', 'users.job_title_id', '=', 'job_titles.id')
            ->orderBy('positions.level', 'asc')
            ->orderBy('job_titles.level', 'asc')
            ->select('users.*');
    }
}
