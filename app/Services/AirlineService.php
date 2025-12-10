<?php

namespace App\Services;

use App\Repositories\AirlineRepository;

class AirlineService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(AirlineRepository::class);
    }
}
