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

    protected function applySearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q
                ->where('name', 'like', $search)
                ->orWhere('code', 'like', $search);
        });
    }
}
