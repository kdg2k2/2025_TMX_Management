<?php

namespace App\Repositories;

use App\Models\ContractorExperience;

class ContractorExperienceRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractorExperience();
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
                ]
            ]
        ];
    }
}
