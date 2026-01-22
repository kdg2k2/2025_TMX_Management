<?php

namespace App\Repositories;

use App\Models\ContractProfessionals;

class ContractProfessionalRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractProfessionals();
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
