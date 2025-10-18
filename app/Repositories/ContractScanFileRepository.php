<?php

namespace App\Repositories;

use App\Models\ContractScanFile;

class ContractScanFileRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractScanFile();
        $this->relations = [
            'type',
            'createdBy',
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['contract_id']))
            $query->where('contract_id', $request['contract_id']);
        if (isset($request['type_id']))
            $query->where('type_id', $request['type_id']);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'createdBy' => ['name'],
                'type' => ['name'],
            ]
        ];
    }
}
