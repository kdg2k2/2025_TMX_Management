<?php

namespace App\Repositories;

use App\Models\OfficialDocumentType;

class OfficialDocumentTypeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new OfficialDocumentType();
        $this->relations = [
            'createdBy',
        ];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => ['name', 'description'],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'createdBy' => ['name'],
            ]
        ];
    }
}
