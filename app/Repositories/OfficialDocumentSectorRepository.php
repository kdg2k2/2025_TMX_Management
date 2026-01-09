<?php

namespace App\Repositories;

use App\Models\OfficialDocumentSector;

class OfficialDocumentSectorRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new OfficialDocumentSector();
        $this->relations = [
            'createdBy:id,name',
            'users:id,name',
        ];
    }

    public function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
                'description',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'createdBy' => ['name'],
                'users' => ['name'],
            ]
        ];
    }
}
