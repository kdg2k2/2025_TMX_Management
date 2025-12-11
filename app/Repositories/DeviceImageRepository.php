<?php

namespace App\Repositories;

use App\Models\DeviceImage;

class DeviceImageRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new DeviceImage();
        $this->relations = [];
    }
}