<?php

namespace App\Services;

use App\Repositories\ContractDisbursementRepository;

class ContractDisbursementService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractDisbursementRepository::class);
    }
}
