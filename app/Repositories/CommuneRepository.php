<?php

namespace App\Repositories;

use App\Models\Commune;

class CommuneRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Commune();
    }

    public function findByCode(int $code)
    {
        return $this->model->where('code', $code)->first();
    }

    public function list(array $request = [])
    {
        $query = $this->model->orderByDesc('code');
        if (isset($request['paginate']) && $request['paginate'] == '1') {
            return $query->paginate($request['per_page'] ?? 10);
        }
        return $query->get()->toArray();
    }
}
