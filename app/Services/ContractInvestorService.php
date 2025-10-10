<?php
namespace App\Services;

use App\Repositories\ContractInvestorRepository;

class ContractInvestorService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractInvestorRepository::class);
    }
}
