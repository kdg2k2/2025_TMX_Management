<?php

namespace App\Repositories;

use App\Models\DossierHandoverDetail;

class DossierHandoverDetailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new DossierHandoverDetail();
        $this->relations = [];
    }
}