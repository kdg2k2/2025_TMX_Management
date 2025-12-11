<?php

namespace App\Services;

use App\Repositories\DeviceTypeRepository;

class DeviceTypeService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(DeviceTypeRepository::class);
    }
}
