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
        private UserService $userService,
    ) {
        $this->repository = app(ContractProductRepository::class);
    }

    public function getBaseDataForLView()
    {
        return [
            'minuteStatuses' => $this->contractProductMinuteService->getStatus(),
            'users' => $this->userService->list([
                'load_relations' => false,
                'columns' => [
                    'id',
                    'name',
                ]
            ]),
        ];
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['product_minutes']))
            $array['product_minutes'] = app(ContractProductMinuteService::class)->formatRecords($array['product_minutes']);
        $array = array_merge($array, $this->isProductInspection($array['product_inspection'] ?? []));
        return $array;
    }

    private function isProductInspection(array $data)
    {
        $last = collect($data)->sortByDesc('created_at')->first() ?? [];
        if (isset($last['status']) && $last['status'] == 'request')
            return [
                'is_inspection_requested' => true,
                'is_inspection_created_by_auth' => auth()->id() == 1 ? true : $last['created_by'] == auth()->id(),
                'is_auth_inspector' => auth()->id() == 1 ? true : $last['contract']['inspector_user_id'] == auth()->id(),
            ];
        return [
            'is_inspection_requested' => false,
            'is_inspection_created_by_auth' => false,
            'is_auth_inspector' => false,
        ];
    }

    public function getContractYears(int $contractId, bool $returnAll = false)
    {
        $contract = $this->contractService->findById($contractId, true)->load('manyYears');
        return $returnAll ? $contract : ($contract['manyYears'] ?? []);
    }
}
