<?php

namespace App\Repositories;

use App\Models\PersonnelCustomField;

class PersonnelCustomFieldRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new PersonnelCustomField();
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

    public function getFields(){
        return $this->model->pluck('field')->toArray();
    }
}
