<?php
namespace App\Repositories;

use App\Models\ContractAppendix;
use App\Repositories\BaseRepository;

class ContractAppendixRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractAppendix();
        $this->relations = [
            'createdBy',
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['contract_id']))
            $query->where('contract_id', $request['contract_id']);
    }

    protected function applySearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q
                ->where('content', 'like', $search)
                ->orWhere('renewal_date', 'like', $search)
                ->orWhere('renewal_end_date', 'like', $search)
                ->orWhere('adjusted_value', 'like', $search)
                ->orWhere('note', 'like', $search)
                ->orWhereHas('createdBy', function ($q) use ($search) {
                    $q->where('name', 'like', $search);
                });
        });
    }

    public function getMaxTimesByContractId(int $contractId)
    {
        return $this->model->where('contract_id', $contractId)->max('times');
    }
}
