<?php

namespace App\Repositories;

use App\Models\ProfessionalRecordUsageRegisterDetail;

class ProfessionalRecordUsageRegisterDetailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ProfessionalRecordUsageRegisterDetail();
        $this->relations = [];
    }
}
