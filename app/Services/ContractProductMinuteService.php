<?php

namespace App\Services;

use App\Repositories\ContractProductMinuteRepository;

class ContractProductMinuteService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractProductMinuteRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);
        if (isset($array['file_path']))
            $array['file_path'] = $this->getAssetUrl($array['file_path']);
        return $array;
    }

    public function getStatus($key = null)
    {
        return $this->repository->getStatus($key);
    }
}
