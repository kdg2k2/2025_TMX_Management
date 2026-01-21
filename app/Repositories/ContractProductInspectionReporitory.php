<?php

namespace App\Repositories;

use App\Models\ContractProductInspection;

class ContractProductInspectionReporitory extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractProductInspection();
        $this->relations = [
            'years',
            'createdBy:id,name',
            'supportedBy:id,name',
            'inspectorUser:id,name',
            'contract' => fn($q) => $q->with([
                'investor',
                'inspectorUser:id,name',
                'executorUser:id,name',
                'professionals.user:id,name',
                'intermediateCollaborators.user:id,name',
            ])
        ];
    }

    public function getStatus($key = null)
    {
        return $this->model->getStatus($key);
    }
}
