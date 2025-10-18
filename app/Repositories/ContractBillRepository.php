<?php
namespace App\Repositories;

use App\Models\ContractBill;

class ContractBillRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractBill();
        $this->relations = [
            'createdBy',
            'billCollector',
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['contract_id']))
            $query->where('contract_id', $request['contract_id']);
        if (isset($request['bill_collector']))
            $query->where('bill_collector', $request['bill_collector']);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'amount',
                'duration',
                'content_in_the_estimate',
                'note',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'createdBy' => ['name'],
                'billCollector' => ['name'],
            ]
        ];
    }
}
