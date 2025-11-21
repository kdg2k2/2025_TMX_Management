<?php

namespace App\Repositories;

use App\Models\ProfessionalRecordHandoverDetail;

class ProfessionalRecordHandoverDetailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ProfessionalRecordHandoverDetail();
        $this->relations = [];
    }
}
