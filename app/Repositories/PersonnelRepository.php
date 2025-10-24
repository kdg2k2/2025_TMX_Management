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
            'personnelPivotPersonnelCustomField.personnelCustomField' => fn($q) => $q->orderBy('z_index', 'desc'),
        ];
    }

    public function applyListFilters($query, array $request)
    {
        if (isset($request['personnel_unit_id']))
            $query->where('personnel_unit_id', $request['personnel_unit_id']);
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
