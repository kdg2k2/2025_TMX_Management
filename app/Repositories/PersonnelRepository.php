<?php

namespace App\Repositories;

use App\Models\Personnel;

class PersonnelRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Personnel();
        $this->relations = [];
    }
}