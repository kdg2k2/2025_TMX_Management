<?php

namespace App\Repositories;

use App\Models\SystemConfig;

class SystemConfigRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new SystemConfig();
        $this->relations = [];
    }

    public function getByKey($key)
    {
        return $this->model->where('key', $key)->first();
    }
}
