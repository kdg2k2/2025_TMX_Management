<?php

namespace App\Services;

use App\Repositories\ContractFinanceRepository;
use Exception;

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

    public function beforeStore(array $request)
    {
        $isJointVentureContract = $this->repository->isJointVentureContract($request['contract_id']);
        if ($isJointVentureContract['isJointVentureContract'] && $isJointVentureContract['count'] > 0)
            throw new Exception('Không thể thêm đơn vị do gói thầu không phải gói liên danh');

        return $request;
    }
}
