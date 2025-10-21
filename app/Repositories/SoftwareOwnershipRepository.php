<?php

namespace App\Repositories;

use App\Models\SoftwareOwnership;

class SoftwareOwnershipRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new SoftwareOwnership();
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
