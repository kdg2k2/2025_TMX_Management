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

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'content',
                'renewal_date',
                'renewal_end_date',
                'adjusted_value',
                'note',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'createdBy' => ['name'],
            ]
        ];
    }

    public function getMaxTimesByContractId(int $contractId)
    {
        return $this->model->where('contract_id', $contractId)->max('times');
    }
}
