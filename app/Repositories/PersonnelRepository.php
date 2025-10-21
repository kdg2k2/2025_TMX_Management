<?php

namespace App\Repositories;

use App\Models\Personnel;

class PersonnelRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Personnel();
        $this->relations = [
            'createdBy',
            'personnelUnit',
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
            'relations' => [
                'createdBy' => [
                    'name',
                ],
                'personnelUnit' => [
                    'name',
                ],
            ]
        ];
    }
}
