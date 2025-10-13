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
        private ProvinceService $provinceService
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

        $array['is_contract_many_year'] = (isset($array['many_years']) && count($array['many_years']) > 0) ? true : false;

        return $array;
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $scopes = $request['contract_scopes'] ?? [];
            $professionals = $request['professional_ids'] ?? [];
            $disbursement = $request['disbursement_ids'] ?? [];
            $instructors = $request['instructor_ids'] ?? [];
            $manyYears = $request['many_years'] ?? [];
            $intermediateCollaborator = $request['intermediate_collaborator_ids'] ?? [];
            unset(
                $request['contract_scopes'],
                $request['professional_ids'],
                $request['disbursement_ids'],
                $request['instructor_ids'],
                $request['many_years'],
                $request['intermediate_collaborator_ids'],
            );

            $data = $this->repository->store($request);

            $this->contractScope($data, $scopes);
            $this->contractProfessionals($data, $professionals);
            $this->contractDisbursement($data, $disbursement);
            $this->contractInstructor($data, $instructors);
            $this->contractManyYear($data, $manyYears);
            $this->contractIntermediateCollaborator($data, $intermediateCollaborator);
        }, true);
    }

    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $scopes = $request['contract_scopes'] ?? [];
            $professionals = $request['professional_ids'] ?? [];
            $disbursement = $request['disbursement_ids'] ?? [];
            $instructors = $request['instructor_ids'] ?? [];
            $manyYears = $request['many_years'] ?? [];
            $intermediateCollaborator = $request['intermediate_collaborator_ids'] ?? [];
            
            unset(
                $request['contract_scopes'],
                $request['professional_ids'],
                $request['disbursement_ids'],
                $request['instructor_ids'],
                $request['many_years'],
                $request['intermediate_collaborator_ids'],
            );

            $data = $this->repository->update($request);

            $this->contractScope($data, $scopes);
            $this->contractProfessionals($data, $professionals);
            $this->contractDisbursement($data, $disbursement);
            $this->contractInstructor($data, $instructors);
            $this->contractManyYear($data, $manyYears);
            $this->contractIntermediateCollaborator($data, $intermediateCollaborator);
        }, true);
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

        if ($columnName == 'intermediateCollaborator')
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
        $this->syncRelationship($contract, 'disbursement', $ids);
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
        $this->syncRelationship($contract, 'intermediateCollaborator', $ids);
    }
}
