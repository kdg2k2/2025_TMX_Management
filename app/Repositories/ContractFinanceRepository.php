<?php

namespace App\Repositories;

use App\Models\ContractFinance;

class ContractFinanceRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractFinance();
        $this->relations = [];
    }
}