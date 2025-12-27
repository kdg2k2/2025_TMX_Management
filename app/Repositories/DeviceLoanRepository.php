<?php

namespace App\Repositories;

use App\Models\DeviceLoan;

class DeviceLoanRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new DeviceLoan();
        $this->relations = [
            'device' => app(DeviceRepository::class)->relations,
            'createdBy:id,name,path',
            'approvedBy:id,name,path',
        ];
    }

    public function getStatus($key = null)
    {
        return $this->model->getStatus($key);
    }
}
