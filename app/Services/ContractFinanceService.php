<?php

namespace App\Services;

use App\Repositories\ContractFinanceRepository;

class ContractFinanceService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractFinanceRepository::class);
    }
}
