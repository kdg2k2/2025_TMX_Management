<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new User();
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function list(array $request = [], ?callable $searchFunc = null)
    {
        $searchFunc = function ($query) use ($request) {
            $query
                ->where('name', 'like', "%{$request['search']}%")
                ->orWhere('email', 'like', "%{$request['search']}%");
        };

        return parent::list($request, $searchFunc);
    }
}
