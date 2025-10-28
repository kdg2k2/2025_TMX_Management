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
        private ContractScanFileTypeService $contractScanFileTypeService,
        private ContractUnitService $contractUnitService,
        private ContractFinanceService $contractFinanceService,
        private ContractAppendixService $contractAppendixService,
        private StringHandlerService $stringHandlerService
    ) {
        $this->repository = app(ContractRepository::class);
    }

    public function getBaseListViewData()
    {
        $res = [];
        $res['fileTypes'] = $this->contractFileTypeService->list();
        $res['scanFileTypes'] = $this->contractScanFileTypeService->list();
        $res['users'] = $this->userService->list([
            'load_relations' => false,
            'columns' => [
                'id',
                'name',
            ]
        ]);
        $res['contractUnits'] = $this->contractUnitService->list();
        $res['financeRoles'] = $this->contractFinanceService->getRole();
        return $res;
    }

    public function getCreateOrUpdateBaseData(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->findById($id, true, true);

        $res['users'] = $this->userService->list([
            'load_relations' => false,
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

        $array['acceptance_value'] = $this->getAcceptanceValue($array['id']);

        return $array;
    }

    // 18/10/2025 a Huân yc ưu tiên lấy con số điều chỉnh trong phụ lục hiển thị làm giá trị nghiệm thu
    private function getAcceptanceValue(int $contractId)
    {
        $data = $this->contractAppendixService->list([
            'contract_id' => $contractId,
        ]);
        $adjustedValues = array_filter(array_column($data, 'adjusted_value'));
        // Ưu tiên lấy con số điều chỉnh trong phụ lục mới nhất
        if (isset($adjustedValues[0]))
            return $adjustedValues[0];

        // nếu ko có số điều chỉnh trong phụ lục thì trả về con số nghiệm thu được nhập trong tài chính
        $acceptanceValue = array_sum(array_column($this->contractFinanceService->list(['contract_id' => $contractId]), 'acceptance_value'));
        if (isset($acceptanceValue))
            return $acceptanceValue;

        // nếu ko có con số nghiệm thu nhập trong tài chính thì hiển thị luôn tổng giá trị hợp đồng
        return $data['contract']['contract_value'] ?? '';
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

    private function initGoogleDriveFolders(int $id)
    {
        $data = $this->repository->findById($id);
        $structure = [
            'Project' => [
                $data['year'] => [
                    $this->stringHandlerService->createPascalSlug($data['short_name']) => [
                        'Contracts',
                        'ProposalEstimateBudget',
                        // 'Disbursement', // file phân bổ đẫ là 1 trong trong loại file của hợp đồng rồi nên ko tạo folder riêng
                        'Products' => [
                            '1.MainProducts',
                            '2.IntermediaProducts',
                            '3.Documentary_Decisions',
                            '4.DataReferences',
                        ],
                    ]
                ]
            ]
        ];

        \App\Jobs\InitFoldersOnDriveJob::dispatch($structure);
    }

    public function getFolderOnGoogleDrive(Contract $data)
    {
        return "Project/{$data['year']}/{$this->stringHandlerService->createPascalSlug($data['short_name'])}";
    }

    private function handleFilesAndRelations(Contract $data, array $extracted, bool $isUpdate = false): void
    {
        $fields = [
            'path_file_full',
            'path_file_short',
        ];

        $this->initGoogleDriveFolders($data['id']);
        foreach ($fields as $field) {
            if ($extracted[$field]) {
                $oldFile = $isUpdate ? $data[$field] : null;
                $data[$field] = $this->handlerUploadFileService->storeAndRemoveOld($extracted[$field], $this->repository->model->getTable(), $field, $oldFile);
                $data->save();

                \App\Jobs\UploadFileToDriveJob::dispatch(
                    $this->handlerUploadFileService->getAbsolutePublicPath($data[$field]),
                    $this->getFolderOnGoogleDrive($data),
                    null,
                    false,
                    false,
                    $oldFile ? $this->getFolderOnGoogleDrive($data) . '/' . basename($oldFile) : null
                );
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
        if ($entity['path_file_short'])
            \App\Jobs\DeleteFileFromDriveJob::dispatch($this->getFolderOnGoogleDrive($entity) . '/' . basename($entity['path_file_short']));
        if ($entity['path_file_full'])
            \App\Jobs\DeleteFileFromDriveJob::dispatch($this->getFolderOnGoogleDrive($entity) . '/' . basename($entity['path_file_full']));
    }

    public function getMembers(int $id)
    {
        $data = $this->repository->findById($id);
        return [
            'accounting_contact' => $data['accountingContact'] ?? [],
            'inspector_user' => $data['inspectorUser'] ?? [],
            'executor_user' => $data['executorUser'] ?? [],
            'instructors' => $data['instructors'] ?? [],
            'professionals' => $data['professionals'] ?? [],
            'disbursements' => $data['disbursements'] ?? [],
            'intermediate_collaborators' => $data['intermediateCollaborators'] ?? [],
        ];
    }

    public function getMemberEmails(int $id, array $types = [])
    {
        $members = $this->getMembers($id);
        $users = collect($types)
            ->flatMap(fn($type) => $members[$type] ?? [])
            ->unique('id')
            ->filter()
            ->values()
            ->toArray();

        return $this->userService->getEmails($users);
    }
}
