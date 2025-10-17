<?php

namespace App\Repositories;

use App\Models\ContractPayment;

class ContractPaymentRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractPayment();
        $this->relations = [];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['contract_finance_id']))
            $query->where('contract_finance_id', $request['contract_finance_id']);
    }
}
