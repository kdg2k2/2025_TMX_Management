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
        if (isset($request['year']))
            $query->where('year', $request['year']);
        if (isset($request['years']))
            $query->whereIn('year', $request['years']);
    }

    public function deleteByContractIdAndYear(int $contractId, int $year)
    {
        $this->model->where('contract_id', $contractId)->where('year', $year)->delete();
    }
}
