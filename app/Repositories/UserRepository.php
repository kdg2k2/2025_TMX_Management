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
            'department',
            'position',
            'jobTitle',
        ];
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function list(array $request = [], ?callable $searchFunc = null)
    {
        $searchFunc = function ($query) use ($request) {
            if (empty($request['search']))
                return;

            $search = "%{$request['search']}%";
            $query->where(function ($q) use ($search) {
                $q
                    ->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('citizen_identification_number', 'like', $search)
                    ->orWhereHas('role', function ($q) use ($search) {
                        $q->where('name', 'like', $search);
                    })
                    ->orWhereHas('department', function ($q) use ($search) {
                        $q->where('name', 'like', $search);
                    })
                    ->orWhereHas('position', function ($q) use ($search) {
                        $q->where('name', 'like', $search);
                    })
                    ->orWhereHas('jobTitle', function ($q) use ($search) {
                        $q->where('name', 'like', $search);
                    });
            });
        };

        return parent::list($request, $searchFunc);
    }
}
