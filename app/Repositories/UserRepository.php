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

    public function findByGoogleId(string $googleId)
    {
        return $this->model->where('google_id', $googleId)->first();
    }

    public function list(array $request = [])
    {
        $query = $this->model->orderByDesc('id');

        if (!empty($request['search'])) {
            $query->where(function ($q) use ($request) {
                $q
                    ->where('name', 'like', "%{$request['search']}%")
                    ->orWhere('email', 'like', "%{$request['search']}%");
            });
        }

        return $query->get()->toArray();
    }
}
