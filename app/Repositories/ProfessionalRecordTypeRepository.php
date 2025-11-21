<?php

namespace App\Repositories;

use App\Models\ProfessionalRecordType;

class ProfessionalRecordTypeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ProfessionalRecordType();
        $this->relations = [
            'createdBy',
        ];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
                'unit',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => []
        ];
    }
}
