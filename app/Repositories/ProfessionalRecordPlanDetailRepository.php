<?php

namespace App\Repositories;

use App\Models\ProfessionalRecordPlanDetail;

class ProfessionalRecordPlanDetailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ProfessionalRecordPlanDetail();
        $this->relations = [];
    }
}
