<?php

namespace App\Repositories;

use App\Models\DeviceType;

class DeviceTypeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new DeviceType();
        $this->relations = [];
    }
}