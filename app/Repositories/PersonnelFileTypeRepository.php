<?php

namespace App\Repositories;

use App\Models\PersonnelFileType;

class PersonnelFileTypeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new PersonnelFileType();
        $this->relations = [
            'extensions.extension',
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
                'extensions.extension' => [
                    'name',
                ],
            ]
        ];
    }
}
