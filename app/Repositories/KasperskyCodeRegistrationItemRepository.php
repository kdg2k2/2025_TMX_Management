<?php

namespace App\Repositories;

use App\Models\KasperskyCodeRegistrationItem;

class KasperskyCodeRegistrationItemRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new KasperskyCodeRegistrationItem();
        $this->relations = [];
    }
}