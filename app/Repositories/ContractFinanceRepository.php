<?php

namespace App\Repositories;

use App\Models\ContractFinance;
use Exception;

class ContractFinanceRepository extends BaseRepository
{
    public function __construct(
        private ContractRepository $contractRepository
    ) {
        $this->model = new ContractFinance();
        $this->relations = [
            'contractUnit',
            'advancePayment',
            'payment',
        ];
    }

    public function getRole($key = null)
    {
        return $this->model->getRole($key);
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['contract_id']))
            $query->where('contract_id', $request['contract_id']);
    }

    public function isJointVentureContract(int $contractId)
    {
        return [
            'isJointVentureContract' => $this->contractRepository->isJointVentureContract($contractId),
            'count' => $this->model->where('contract_id', $contractId)->count(),
        ];
    }
}
