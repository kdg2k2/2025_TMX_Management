<?php
namespace App\Repositories;

use App\Models\ContractFile;

class ContractFileRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractFile();
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
            'text' => [
                'updated_content',
                'note',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'createdBy' => ['name'],
                'type' => ['name'],
            ]
        ];
    }
}
