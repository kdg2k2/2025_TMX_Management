<?php

namespace App\Services;

use App\Repositories\ContractMainProductRepository;

class ContractMainProductService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractMainProductRepository::class);
    }
}
