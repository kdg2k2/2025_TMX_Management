<?php

namespace App\Repositories;

use App\Models\PersonnelUnit;

class PersonnelUnitRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new PersonnelUnit();
        $this->relations = [
            'createdBy',
        ];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
                'short_name',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'createdBy' => [
                    'name',
                ]
            ]
        ];
    }
}
