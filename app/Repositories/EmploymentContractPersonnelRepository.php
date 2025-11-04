<?php

namespace App\Repositories;

use App\Models\EmploymentContractPersonnel;

class EmploymentContractPersonnelRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new EmploymentContractPersonnel();
        $this->relations = [
            'createdBy',
            'employmentContractPersonnelPivotEmploymentContractPersonnelCustomField.employmentContractPersonnelCustomField' => fn($q) => $q->orderBy('z_index', 'desc'),
        ];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
                'citizen_identification_number',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'createdBy' => [
                    'name',
                ],
            ]
        ];
    }
}
