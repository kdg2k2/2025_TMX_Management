<?php

namespace App\Repositories;

use App\Models\ContractProductInspectionYear;

class ContractProductInspectionYearRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractProductInspectionYear();
        $this->relations = [];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['contract_product_inspection_id']))
            $query->where('contract_product_inspection_id', $request['contract_product_inspection_id']);
    }
}
