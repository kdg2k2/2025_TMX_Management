<?php

namespace App\Repositories;

use App\Models\ContractManyYear;

class ContractManyYearRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractManyYear();
        $this->relations = [];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['contract_id']))
            $query->where('contract_id', $request['contract_id']);
    }
}
