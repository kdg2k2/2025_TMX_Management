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

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['device_id']))
            $query->where('device_id', $request['device_id']);
    }
}
