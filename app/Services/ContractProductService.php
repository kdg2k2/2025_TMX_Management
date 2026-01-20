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
            'productStatuses' => $this->contractService->getIntermediateProductStatus(),
        ];
    }
}
