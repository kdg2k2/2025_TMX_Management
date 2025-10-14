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
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(ContractRepository::class);
    }

    public function getCreateOrUpdateData(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->findById($id, true, true);

        $res['users'] = $this->userService->list([
            'columns' => [
                'id',
                'name',
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

        $array['is_contract_many_year'] = (isset($array['many_years']) && count($array['many_years']) > 0) ? 1 : 0;

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
        if ($extracted['path_file_full']) {
            $oldFile = $isUpdate ? $data['path_file_full'] : null;
            $data['path_file_full'] = $this->handlerUploadFileService->storeAndRemoveOld($extracted['path_file_full'], 'contract', 'full', $oldFile);
            $data->save();
        }

        if ($extracted['path_file_short']) {
            $oldFile = $isUpdate ? $data['path_file_short'] : null;
            $data['path_file_short'] = $this->handlerUploadFileService->storeAndRemoveOld($extracted['path_file_short'], 'contract', 'short', $oldFile);
            $data->save();
        }

        $this->contractScope($data, $extracted['scopes'] ?? []);
        $this->contractProfessionals($data, $extracted['professionals'] ?? []);
        $this->contractDisbursement($data, $extracted['disbursements'] ?? []);
        $this->contractInstructor($data, $extracted['instructors'] ?? []);
        $this->contractManyYear($data, $extracted['many_years'] ?? []);
        $this->contractIntermediateCollaborator($data, $extracted['intermediate_collaborators'] ?? []);
    }

    private function syncRelationship(Contract $contract, string $relation, array $items, string $columnName = 'user_id')
    {
        $contract->$relation()->delete();

        if (count($items) == 0)
            return;

        $data = collect($items)->map(function ($item) use ($contract, $columnName) {
            return [
                $columnName => $item,
                'contract_id' => $contract['id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        if ($columnName == 'intermediateCollaborators')
            dd(
                $data, $relation, $items, $columnName
            );

        if (!empty($data))
            $contract->$relation()->insert($data);
    }

    private function contractScope(Contract $contract, array $codes)
    {
        $this->syncRelationship($contract, 'scopes', $codes, 'province_code');
    }

    private function contractProfessionals(Contract $contract, array $ids)
    {
        $this->syncRelationship($contract, 'professionals', $ids);
    }

    private function contractDisbursement(Contract $contract, array $ids)
    {
        $this->syncRelationship($contract, 'disbursements', $ids);
    }

    private function contractInstructor(Contract $contract, array $ids)
    {
        $this->syncRelationship($contract, 'instructors', $ids);
    }

    private function contractManyYear(Contract $contract, array $years)
    {
        $this->syncRelationship($contract, 'manyYears', $years, 'year');
    }

    private function contractIntermediateCollaborator(Contract $contract, array $ids)
    {
        $this->syncRelationship($contract, 'intermediateCollaborators', $ids);
    }
}
