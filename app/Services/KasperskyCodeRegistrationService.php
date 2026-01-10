<?php

namespace App\Services;

use App\Repositories\KasperskyCodeRegistrationRepository;

class KasperskyCodeRegistrationService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(KasperskyCodeRegistrationRepository::class);
    }
}
