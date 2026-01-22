<?php

namespace App\Services;

use App\Repositories\SystemConfigRepository;

class SystemConfigService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(SystemConfigRepository::class);
    }
}
