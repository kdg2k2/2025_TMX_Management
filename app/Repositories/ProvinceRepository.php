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

    protected function applySearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q
                ->where('name', 'like', $search)
                ->orWhere('code', 'like', $search);
        });
    }
}
