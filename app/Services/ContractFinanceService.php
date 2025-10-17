<?php

namespace App\Services;

use App\Repositories\ContractFinanceRepository;

class ContractFinanceService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractFinanceRepository::class);
    }

    public function getRole($key = null)
    {
        return $this->repository->model->getRole($key);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['role']))
            $array['role'] = $this->getRole($array['role']);
        return $array;
    }
}
