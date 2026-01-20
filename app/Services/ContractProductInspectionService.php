<?php

namespace App\Services;

use App\Repositories\ContractProductInspectionReporitory;

class ContractProductInspectionService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractProductInspectionReporitory::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);
        if (isset($array['issue_file_path']))
            $array['issue_file_path'] = $this->getAssetUrl($array['issue_file_path']);
        return $array;
    }
}
