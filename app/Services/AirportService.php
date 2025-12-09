<?php

namespace App\Services;

use App\Repositories\AirportRepository;

class AirportService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(AirportRepository::class);
    }
}
