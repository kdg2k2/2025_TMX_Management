<?php

namespace App\Repositories;

use App\Models\EmploymentContractPersonnelCustomField;

class EmploymentContractPersonnelCustomFieldRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new EmploymentContractPersonnelCustomField();
        $this->relations = [
            'createdBy',
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
            ]
        ];
    }

    public function getType($key = null)
    {
        return $this->model->getType($key);
    }
}
