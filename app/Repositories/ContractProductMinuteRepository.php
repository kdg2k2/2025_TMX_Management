<?php

namespace App\Repositories;

use App\Models\ContractProductMinute;

class ContractProductMinuteRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractProductMinute();
        $this->relations = [];
    }

    public function getStatus($key = null)
    {
        return $this->model->getStatus($key);
    }

    protected function applyListFilters($query, array $request)
    {
        foreach ([
            'status',
            'contract_id',
        ] as $item)
            if (isset($request[$item]))
                $query->where($item, $request[$item]);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'issue_note'
            ],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'contract' => ['name']
            ]
        ];
    }
}
