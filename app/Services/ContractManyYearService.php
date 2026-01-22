<?php

namespace App\Services;

use App\Repositories\ContractManyYearRepository;

class ContractManyYearService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractManyYearRepository::class);
    }
}
