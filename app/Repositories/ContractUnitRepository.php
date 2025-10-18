<?php

namespace App\Repositories;

use App\Models\ContractUnit;

class ContractUnitRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractUnit();
        $this->relations = [];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
                'address',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => []
        ];
    }
}
