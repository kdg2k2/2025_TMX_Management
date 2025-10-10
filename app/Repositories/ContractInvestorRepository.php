<?php
namespace App\Repositories;

use App\Models\ContractInvestor;

class ContractInvestorRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractInvestor();
    }
}
