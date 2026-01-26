<?php

namespace App\Repositories;

use App\Models\ContractPersonnel;

class ContractPersonnelRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractPersonnel();
        $this->relations = [
            'personnel'
        ];
    }

    public function isInContract($key = null)
    {
        return $this->model->isInContract($key);
    }

    protected function applyListFilters($query, array $request)
    {
        foreach ([
            'contract_id',
            'personnel_id',
            'is_in_contract',
        ] as $item)
            if (isset($request[$item]))
                $query->where($item, $request[$item]);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'position',
                'position_en',
                'mobilized_unit',
                'mobilized_unit_en',
                'task',
                'task_en',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => []
        ];
    }

    public function deleteByContractId(int $contractId)
    {
        $this->model->where('contract_id', $contractId)->delete();
    }

    public function synthetic(array $request)
    {
        return $this
            ->model
            ->with([
                'contract' => fn($q) => $q->with([
                    'investor:id,name_vi,name_en',
                    'scopes.province:code,name',
                ])->select([
                    'id',
                    'investor_id',
                    'year',
                    'contract_number',
                    'name',
                    'name_en',
                    'contract_value',
                    'signed_date',
                    'acceptance_date',
                ])
            ])
            ->where('personnel_id', $request['personnel_id'])
            ->when($request['contract_id'] ?? null,
                fn($q, $v) => $q->where('contract_id', $v))
            ->whereHas('contract', fn($q) =>
                $q
                    ->when($request['year'] ?? null,
                        fn($sq, $v) => $sq->where('year', $v))
                    ->when($request['investor_id'] ?? null,
                        fn($sq, $v) => $sq->where('investor_id', $v)))
            ->get()
            ->toArray();
    }
}
