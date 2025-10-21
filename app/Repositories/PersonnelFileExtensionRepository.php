<?php

namespace App\Repositories;

use App\Models\PersonnelFileExtension;

class PersonnelFileExtensionRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new PersonnelFileExtension();
        $this->relations = [];
    }
}