<?php

namespace App\Repositories;

use App\Models\DossierUsageRegisterDetail;

class DossierUsageRegisterDetailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new DossierUsageRegisterDetail();
        $this->relations = [];
    }
}