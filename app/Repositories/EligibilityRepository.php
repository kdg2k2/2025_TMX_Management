<?php

namespace App\Repositories;

use App\Models\Eligibility;

class EligibilityRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Eligibility();
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
