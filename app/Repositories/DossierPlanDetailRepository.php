<?php

namespace App\Repositories;

use App\Models\DossierPlanDetail;

class DossierPlanDetailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new DossierPlanDetail();
        $this->relations = [];
    }
}