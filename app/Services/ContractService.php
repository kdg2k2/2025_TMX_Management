<?php
namespace App\Services;

use App\Repositories\ContractRepository;

class ContractService extends BaseService
{
    public function __construct(
        private UserService $userService,
        private ContractTypeService $contractTypeService,
        private ContractInvestorService $contractInvestorService
    ) {
        $this->repository = app(ContractRepository::class);
    }

    public function getCreateOrUpdateData(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->repository->findById($id);

        $res['users'] = $this->userService->list([
            'columns' => [
                'id',
                'name',
            ]
        ]);
        $res['types'] = $this->contractTypeService->list();
        $res['investors'] = $this->contractInvestorService->list();

        return $res;
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        if (isset($array['signed_date']))
            $array['signed_date'] = $this->formatDateForPreview($array['signed_date']);
        if (isset($array['effective_date']))
            $array['effective_date'] = $this->formatDateForPreview($array['effective_date']);
        if (isset($array['end_date']))
            $array['end_date'] = $this->formatDateForPreview($array['end_date']);
        if (isset($array['completion_date']))
            $array['completion_date'] = $this->formatDateForPreview($array['completion_date']);
        if (isset($array['acceptance_date']))
            $array['acceptance_date'] = $this->formatDateForPreview($array['acceptance_date']);
        if (isset($array['liquidation_date']))
            $array['liquidation_date'] = $this->formatDateForPreview($array['liquidation_date']);

        if (isset($array['path_file_full']))
            $array['path_file_full'] = $this->getAssetUrl($array['path_file_full']);
        if (isset($array['path_file_short']))
            $array['path_file_short'] = $this->getAssetUrl($array['path_file_short']);

        if (isset($array['contract_status']))
            $array['contract_status'] = $this->repository->model->getContractStatus($array['contract_status']);
        if (isset($array['intermediate_product_status']))
            $array['intermediate_product_status'] = $this->repository->model->getIntermediateProductStatus($array['intermediate_product_status']);
        if (isset($array['financial_status']))
            $array['financial_status'] = $this->repository->model->getFinancialStatus($array['financial_status']);

        return $array;
    }
}
