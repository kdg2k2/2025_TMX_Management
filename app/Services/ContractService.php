<?php
namespace App\Services;

use App\Models\Contract;
use App\Repositories\ContractRepository;

class ContractService extends BaseService
{
    public function __construct(
        private UserService $userService,
        private ContractTypeService $contractTypeService,
        private ContractInvestorService $contractInvestorService,
        private ProvinceService $provinceService,
        private HandlerUploadFileService $handlerUploadFileService,
        private ContractFileTypeService $contractFileTypeService,
        private ContractUnitService $contractUnitService,
        private ContractFinanceService $contractFinanceService
    ) {
        $this->repository = app(ContractRepository::class);
    }

    public function getBaseListViewData()
    {
        $res = [];
        $res['fileTypes'] = $this->contractFileTypeService->list();
        $res['users'] = $this->userService->list([
            'load_relations' => false,
            'columns' => [
                'id',
                'name',
                'path',
            ]
        ]);
        $res['contractUnits'] = $this->contractUnitService->list();
        $res['financeRoles'] = $this->contractFinanceService->getRole();
        return $res;
    }

    public function getCreateOrUpdateData(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->findById($id, true, true);

        $res['users'] = $this->userService->list([
            'load_relations' => false,
            'columns' => [
                'id',
                'name',
                'path',
            ]
        ]);
        $res['types'] = $this->contractTypeService->list();
        $res['investors'] = $this->contractInvestorService->list();
        $res['contract_status'] = $this->repository->model->getContractStatus();
        $res['intermediate_product_status'] = $this->repository->model->getIntermediateProductStatus();
        $res['financial_status'] = $this->repository->model->getFinancialStatus();

        $res['provinces'] = $this->provinceService->list();

        return $res;
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        $dates = [
            'signed_date',
            'effective_date',
            'end_date',
            'completion_date',
            'acceptance_date',
            'liquidation_date',
        ];
        foreach ($dates as $date)
            if (isset($array[$date]))
                $array[$date] = $this->formatDateForPreview($array[$date]);

        $files = [
            'path_file_full',
            'path_file_short',
        ];
        foreach ($files as $file)
            if (isset($array[$file]))
                $array[$file] = $this->getAssetUrl($array[$file]);

        if (isset($array['contract_status']))
            $array['contract_status'] = $this->repository->model->getContractStatus($array['contract_status']);
        if (isset($array['intermediate_product_status']))
            $array['intermediate_product_status'] = $this->repository->model->getIntermediateProductStatus($array['intermediate_product_status']);
        if (isset($array['financial_status']))
            $array['financial_status'] = $this->repository->model->getFinancialStatus($array['financial_status']);

        $array['is_contract_many_year'] = (isset($array['many_years']) && count($array['many_years']) > 0) ? 1 : 0;

        $array['acceptance_value'] = array_sum(array_column($this->contractFinanceService->list(['contract_id' => $array['id']]), 'acceptance_value'));

        return $array;
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractRelationsAndFiles($request);
            $data = $this->repository->store($request);
            $this->handleFilesAndRelations($data, $extracted);
        }, true);
    }

    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractRelationsAndFiles($request);
            $data = $this->repository->update($request);
            $this->handleFilesAndRelations($data, $extracted, true);
        }, true);
    }

    private function extractRelationsAndFiles(array &$request): array
    {
        $fields = [
            'scopes',
            'professionals',
            'disbursements',
            'instructors',
            'many_years',
            'intermediate_collaborators',
            'path_file_full',
            'path_file_short',
        ];

        $extracted = [];
        foreach ($fields as $field) {
            $extracted[$field] = $request[$field] ?? null;
            unset($request[$field]);
        }

        return $extracted;
    }

    private function handleFilesAndRelations($data, array $extracted, bool $isUpdate = false): void
    {
        $fields = [
            'path_file_full',
            'path_file_short',
        ];
        foreach ($fields as $field) {
            if ($extracted[$field]) {
                $oldFile = $isUpdate ? $data[$field] : null;
                $data[$field] = $this->handlerUploadFileService->storeAndRemoveOld($extracted[$field], 'contract', $field, $oldFile);
                $data->save();
            }
        }

        $this->contractScope($data, $extracted['scopes'] ?? []);
        $this->contractProfessionals($data, $extracted['professionals'] ?? []);
        $this->contractDisbursement($data, $extracted['disbursements'] ?? []);
        $this->contractInstructor($data, $extracted['instructors'] ?? []);
        $this->contractManyYear($data, $extracted['many_years'] ?? []);
        $this->contractIntermediateCollaborator($data, $extracted['intermediate_collaborators'] ?? []);
    }

    private function contractScope(Contract $contract, array $codes)
    {
        $this->syncRelationship($contract, 'contract_id', 'scopes', $codes, 'province_code');
    }

    private function contractProfessionals(Contract $contract, array $ids)
    {
        $this->syncRelationship($contract, 'contract_id', 'professionals', $ids, 'user_id');
    }

    private function contractDisbursement(Contract $contract, array $ids)
    {
        $this->syncRelationship($contract, 'contract_id', 'disbursements', $ids, 'user_id');
    }

    private function contractInstructor(Contract $contract, array $ids)
    {
        $this->syncRelationship($contract, 'contract_id', 'instructors', $ids, 'user_id');
    }

    private function contractManyYear(Contract $contract, array $years)
    {
        $this->syncRelationship($contract, 'contract_id', 'manyYears', $years, 'year');
    }

    private function contractIntermediateCollaborator(Contract $contract, array $ids)
    {
        $this->syncRelationship($contract, 'contract_id', 'intermediateCollaborators', $ids, 'user_id');
    }

    protected function afterDelete($entity)
    {
        $this->handlerUploadFileService->removeFiles([$entity['path_file_short'], $entity['path_file_full']]);
    }
}
