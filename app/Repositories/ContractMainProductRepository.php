<?php

namespace App\Repositories;

use App\Models\ContractMainProduct;

class ContractMainProductRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractMainProduct();
        $this->relations = [];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['contract_id']))
            $query->where('contract_id', $request['contract_id']);
    }
}
