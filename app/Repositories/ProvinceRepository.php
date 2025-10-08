<?php

namespace App\Repositories;

use App\Models\Province;

class ProvinceRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Province();
        $this->relations = [
            'communes',
        ];
    }

    public function findByCode(int $code)
    {
        return $this->model->where('code', $code)->with($this->relations)->first();
    }

    public function list(array $request = [])
    {
        $query = $this->model->orderByDesc('code')->with($this->relations);
        if (isset($request['paginate']) && $request['paginate'] == '1') {
            return $query->paginate($request['per_page'] ?? 10);
        }
        return $query->get()->toArray();
    }
}
