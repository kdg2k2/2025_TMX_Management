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

    protected function applySearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q
                ->where('amount', 'like', $search)
                ->orWhere('duration', 'like', $search)
                ->orWhere('content_in_the_estimate', 'like', $search)
                ->orWhere('note', 'like', $search)
                ->orWhereHas('createdBy', function ($q) use ($search) {
                    $q->where('name', 'like', $search);
                })
                ->orWhereHas('billCollector', function ($q) use ($search) {
                    $q->where('name', 'like', $search);
                });
        });
    }
}
