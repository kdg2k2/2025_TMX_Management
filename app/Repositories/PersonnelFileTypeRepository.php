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
}
