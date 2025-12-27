<?php

namespace App\Repositories;

use App\Models\UserSubEmail;

class UserSubEmailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new UserSubEmail();
        $this->relations = [];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['user_id']))
            $query->where('user_id', $request['user_id']);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'email',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => []
        ];
    }
}
