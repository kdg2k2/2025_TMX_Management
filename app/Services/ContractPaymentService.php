<?php

namespace App\Services;

use App\Repositories\ContractPaymentRepository;

class ContractPaymentService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractPaymentRepository::class);
    }

    protected function afterStore($data, array $request)
    {
        $this->updateTimes($data['contract_finance_id']);
    }

    protected function afterUpdate($data, array $request)
    {
        $this->updateTimes($data['contract_finance_id']);
    }

    protected function afterDelete($entity)
    {
        $this->updateTimes($entity['contract_finance_id']);
    }

    private function updateTimes(int $contractFinanceId)
    {
        $list = $this->repository->list([
            'order_by' => 'id',
            'sort_by' => 'asc',
            'contract_finance_id' => $contractFinanceId,
            'load_relations' => false,
        ]);

        if (count($list) == 0)
            return;

        $list->values()->each(function ($item, $index) {
            $item->update(['times' => $index + 1]);
        });
    }
}
