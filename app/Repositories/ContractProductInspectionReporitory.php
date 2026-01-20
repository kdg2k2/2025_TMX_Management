<?php

namespace App\Repositories;

use App\Models\ContractProductInspection;

class ContractProductInspectionReporitory extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractProductInspection();
        $this->relations = [
            'years'
        ];
    }

    public function getStatus($key = null)
    {
        return $this->model->getStatus($key);
    }
}
