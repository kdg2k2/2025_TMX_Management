<?php

namespace App\Services;

use App\Repositories\ContractProductInspectionYearRepository;

class ContractProductInspectionYearService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractProductInspectionYearRepository::class);
    }
}
