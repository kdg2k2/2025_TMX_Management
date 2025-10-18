<?php

namespace App\Repositories;

use App\Models\ContractUnit;

class ContractUnitRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractUnit();
        $this->relations = [];
    }

    protected function applySearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q
                ->where('name', 'like', $search)
                ->orWhere('address', 'like', $search);
        });
    }
}
