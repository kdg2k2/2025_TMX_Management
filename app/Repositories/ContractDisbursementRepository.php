<?php

namespace App\Repositories;

use App\Models\ContractDisbursement;

class ContractDisbursementRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractDisbursement();
        $this->relations = [
            'user:id,name'
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['contract_id']))
            $query->where('contract_id', $request['contract_id']);
    }
}
