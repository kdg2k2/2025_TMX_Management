<?php

namespace App\Services;

use App\Repositories\ContractProfessionalRepository;

class ContractProfessionalService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractProfessionalRepository::class);
    }
}
