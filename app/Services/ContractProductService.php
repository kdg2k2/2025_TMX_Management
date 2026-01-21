<?php

namespace App\Services;

use App\Repositories\ContractProductRepository;

class ContractProductService extends BaseService
{
    public function __construct(
        private ContractService $contractService,
        private ContractMainProductService $contractMainProductService,
        private ContractIntermediateProductService $contractIntermediateProductService,
        private ContractProductMinuteService $contractProductMinuteService,
    ) {
        $this->repository = app(ContractProductRepository::class);
    }

    public function getBaseDataForLView()
    {
        return [
            'minuteStatuses' => $this->contractProductMinuteService->getStatus(),
        ];
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['product_minutes']))
            $array['product_minutes'] = app(ContractProductMinuteService::class)->formatRecords($array['product_minutes']);
        return $array;
    }

    public function getContractYears(int $contractId, bool $returnAll = false)
    {
        $contract = $this->contractService->findById($contractId, true)->load('manyYears');
        return $returnAll ? $contract : ($contract['manyYears'] ?? []);
    }
}
