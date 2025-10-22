<?php

namespace App\Repositories;

use App\Models\PersonnelFile;

class PersonnelFileRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new PersonnelFile();
        $this->relations = [
            'createdBy',
            'type',
            'personnel',
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['personnel_id']))
            $query->where('personnel_id', $request['personnel_id']);
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
                'createdBy' => [
                    'name',
                ],
                'type' => [
                    'name',
                ],
                'personnel' => [
                    'name',
                ],
            ]
        ];
    }
}
