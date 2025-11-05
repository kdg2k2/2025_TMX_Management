<?php

namespace App\Repositories;

use App\Models\DossierType;

class DossierTypeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new DossierType();
        $this->relations = [
            'createdBy',
        ];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
                'unit',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => []
        ];
    }
}
