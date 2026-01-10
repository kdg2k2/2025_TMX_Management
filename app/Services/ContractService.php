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

        if (!$this->isLocal())
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

                if (!$this->isLocal())
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
        $this->syncRelationship($contract, 'contract_id', 'scopes', array_map(fn($i) => ['province_code' => $i], $codes));
    }

    private function contractProfessionals(Contract $contract, array $ids)
    {
        $this->syncRelationship($contract, 'contract_id', 'professionals', array_map(fn($i) => ['user_id' => $i], $ids));
    }

    private function contractDisbursement(Contract $contract, array $ids)
    {
        $this->syncRelationship($contract, 'contract_id', 'disbursements', array_map(fn($i) => ['user_id' => $i], $ids));
    }

    private function contractInstructor(Contract $contract, array $ids)
    {
        $this->syncRelationship($contract, 'contract_id', 'instructors', array_map(fn($i) => ['user_id' => $i], $ids));
    }

    private function contractManyYear(Contract $contract, array $years)
    {
        $this->syncRelationship($contract, 'contract_id', 'manyYears', array_map(fn($i) => ['year' => $i], $years));
    }

    private function contractIntermediateCollaborator(Contract $contract, array $ids)
    {
        $this->syncRelationship($contract, 'contract_id', 'intermediateCollaborators', array_map(fn($i) => ['user_id' => $i], $ids));
    }

    protected function afterDelete($entity)
    {
        $this->handlerUploadFileService->removeFiles([$entity['path_file_short'], $entity['path_file_full']]);
        if (!$this->isLocal())
            if ($entity['path_file_short'])
                \App\Jobs\DeleteFileFromDriveJob::dispatch($this->getFolderOnGoogleDrive($entity) . '/' . basename($entity['path_file_short']));
        if (!$this->isLocal())
            if ($entity['path_file_full'])
                \App\Jobs\DeleteFileFromDriveJob::dispatch($this->getFolderOnGoogleDrive($entity) . '/' . basename($entity['path_file_full']));
    }

    public function getMembers(int $id)
    {
        $data = $this->repository->findById($id);
        return [
            'accounting_contact' => $data['accountingContact'] ?? [], // phụ trách kế toán
            'inspector_user' => $data['inspectorUser'] ?? [], // người kiểm tra SPTG
            'executor_user' => $data['executorUser'] ?? [], // người thực hiện SPTG
            'instructors' => $data['instructors'] ?? [], // người hướng dẫn
            'professionals' => $data['professionals'] ?? [], // chuyên môn
            'disbursements' => $data['disbursements'] ?? [], // giải ngân
            'intermediate_collaborators' => $data['intermediateCollaborators'] ?? [], // các thành viên hỗ trợ thực hiện SPTG
        ];
    }

    public function getMemberEmails(int $id, array $types = [])
    {
        $members = $this->getMembers($id);
        $userIds = collect($types)
            ->flatMap(fn($type) => $members[$type] ?? [])
            ->unique('id')
            ->map(fn($item) => $item['user']['id'] ?? null)
            ->filter()
            ->values()
            ->toArray();

        return $this->userService->getEmails($userIds);
    }

    public function getYears()
    {
        return $this->repository->getYears();
    }

    public function export()
    {
        return $this->tryThrow(function () {
            $data = $this->list();
            $folder = 'uploads/render/export_contract';
            $this->handlerUploadFileService->cleanupOldOverlapFiles($folder);
            return asset(app(ExcelService::class)->createExcel(
                [
                    (object) [
                        'name' => 'data',
                        'header' => [$this->renderExportExcelHeader($data)],
                        'data' => $this->renderExportExcelData($data),
                        'boldRows' => [1],
                        'boldItalicRows' => [],
                        'italicRows' => [],
                        'centerColumns' => [],
                        'centerRows' => [],
                        'filterStartRow' => 1,
                        'freezePane' => 'freezeTopRow',
                    ]
                ],
                $folder,
                'contracts_' . date('d-m-Y_H-i-s') . '.xlsx'
            ));
        });
    }

    private function renderExportExcelData($data)
    {
        return array_map(fn($i) => [
            $i['id'],
            $i['year'],
            $i['contract_number'],
            $i['name'],
            $i['type']['name'] ?? '',
            $i['investor']['name_vi'] ?? '',
            implode(', ', array_column($i['scopes'], 'name')) ?? '',
            $i['signed_date'],
            $i['contract_value'],
            ($i['contract_value'] ?? 0) - ($i['vat_amount'] ?? 0),
            $i['contract_status']['converted'] ?? '',
            $i['note'],
            $i['name_en'],
            $i['investor']['name_en'] ?? '',
            $i['target_en'],
            $i['main_activities_en'],
            implode(', ', array_map(fn($d) => $d['user']['name'] ?? '', $i['professionals'])),
            implode(', ', array_map(fn($d) => $d['user']['name'] ?? '', $i['disbursements'])),
            $i['a_side'],
            $i['b_side'],
            $i['end_date'],
            implode(', ', array_map(fn($d) => $d['user']['name'] ?? '', $i['instructors'])),
            $i['accounting_contact']['name'] ?? '',
            $i['inspector_user']['name'] ?? '',
            $i['executor_user']['name'] ?? '',
            implode(', ', array_map(fn($d) => $d['user']['name'] ?? '', $i['intermediate_collaborators'])),
            $i['ggdrive_link'] ?? '',
            $i['financial_status']['converted'] ?? '',
            $i['intermediate_product_status']['converted'] ?? '',
        ], $data);
    }

    private function renderExportExcelHeader($data)
    {
        return array_map(fn($i) => [
            'name' => $i,
            'rowspan' => 1,
            'colspan' => 1,
        ], [
            'ID',
            'Năm',
            'Số HĐ',
            'Tên HĐ',
            'Loại HĐ',
            'Chủ đầu tư',
            'Địa điểm',
            'Ngày ký',
            'Giá trị HĐ',
            'Doanh thu',
            'Tình trạng',
            'Ghi chú',
            'Tên HĐ(EN)',
            'Chủ đầu tư (EN)',
            'Mục tiêu (EN)',
            'Hoạt động chính(EN)',
            'PT chuyên môn',
            'PT giải ngân',
            'Ben A',
            'Bên B',
            'Ngày kết thúc',
            'Người HD',
            'Đầu mối kế toán',
            'Người Kiểm tra SP',
            'Người hoàn thiện SPTG',
            'Người Phối hợp SPTG',
            'Link Driver',
            'Tình trạng hồ sơ tài chính',
            'Tình trạng SPTG',
        ]);
    }
}
