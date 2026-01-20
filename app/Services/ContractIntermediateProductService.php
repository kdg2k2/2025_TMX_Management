<?php

namespace App\Services;

use App\Repositories\ContractIntermediateProductRepository;

class ContractIntermediateProductService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractIntermediateProductRepository::class);
    }
}
