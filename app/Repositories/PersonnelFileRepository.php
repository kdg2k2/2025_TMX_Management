<?php

namespace App\Repositories;

use App\Models\PersonnelFile;

class PersonnelFileRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new PersonnelFile();
        $this->relations = [
            'createdBy',
            'type',
            'personnel',
        ];
    }
}
