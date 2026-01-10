<?php

namespace App\Services;

use App\Repositories\KasperskyCodeRegistrationItemRepository;

class KasperskyCodeRegistrationItemService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(KasperskyCodeRegistrationItemRepository::class);
    }
}
