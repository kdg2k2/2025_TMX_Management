<?php

namespace App\Repositories;

use App\Models\Unit;

class UnitRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Unit();
        $this->relations = [
            'province',
            'createdBy'
        ];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => []
        ];
    }
}
