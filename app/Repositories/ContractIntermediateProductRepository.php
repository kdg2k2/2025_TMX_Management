<?php

namespace App\Repositories;

use App\Models\ContractIntermediateProduct;

class ContractIntermediateProductRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractIntermediateProduct();
        $this->relations = [];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['contract_id']))
            $query->where('contract_id', $request['contract_id']);
    }
}
