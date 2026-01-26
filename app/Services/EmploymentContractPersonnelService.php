<?php

namespace App\Services;

use App\Models\EmploymentContractPersonnel;
use App\Repositories\EmploymentContractPersonnelRepository;
use Exception;

class EmploymentContractPersonnelService extends BaseService
{
    public function __construct(
        private EmploymentContractPersonnelCustomFieldService $personnelCustomFieldService,
        private ExcelService $excelService,
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(EmploymentContractPersonnelRepository::class);
    }

    public function getCreateOrUpdateBaseData(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->findById($id, true, true);

        $res['fields'] = $this->personnelCustomFieldService->list();

        return $res;
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractFields($request);
            $data = $this->repository->store($request);
            $this->handleExtracted($data, $extracted);
        }, true);
    }

    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractFields($request);
            $data = $this->repository->update($request);
            $this->handleExtracted($data, $extracted, true);
        }, true);
    }

    private function extractFields(array &$request): array
    {
        $fields = $this->personnelCustomFieldService->getFields();

        $extracted = [];
        foreach ($fields as $field) {
            $extracted[$field] = $request[$field] ?? null;
            unset($request[$field]);
        }

        return $extracted;
    }

    private function handleExtracted(EmploymentContractPersonnel $data, array $extracted, bool $isUpdate = false)
    {
        $extracted = $this->formatFields($data, $extracted);
        $this->employmentContractPersonnelPivotPersonnelCustomField($data, $extracted);
    }

    private function formatFields(EmploymentContractPersonnel $data, array $fields)
    {
        $res = [];
        foreach ($fields as $key => $value) {
            $personnelCustomFieldId = optional($this->personnelCustomFieldService->findByField($key))->id ?? null;
            if (!$personnelCustomFieldId)
                throw new Exception('Không tìm thấy field');

            $res[] = [
                'employment_contract_personnel_id' => $data['id'],
                'employment_contract_personnel_custom_field_id' => $personnelCustomFieldId,
                'value' => $value,
            ];
        }
        return $res;
    }

    private function employmentContractPersonnelPivotPersonnelCustomField(EmploymentContractPersonnel $data, array $pivot)
    {
        $data->employmentContractPersonnelPivotPersonnelCustomField()->delete();

        if (count($pivot) == 0)
            return;

        $data->employmentContractPersonnelPivotPersonnelCustomField()->insert($pivot);
    }

    public function synctheticExcel(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $records = $this->list($request);

            $header = $this->createHeaderSynctheticExcel();
            $data = $this->createDataSynctheticExcel($records ?? []);

            $sheets = [
                (object) [
                    'name' => 'employment_contract_personnel',
                    'header' => $header,
                    'data' => $data,
                    'boldRows' => [1],
                    'boldItalicRows' => [],
                    'italicRows' => [],
                    'centerColumns' => [],
                    'centerRows' => [],
                    'filterStartRow' => 1,
                    'freezePane' => 'freezeTopRow',
                ],
            ];

            $folder='uploads/render/employment_contract_personnel';
            $this->handlerUploadFileService->cleanupOldOverlapFiles($folder);
            return $this->getAssetUrl($this->excelService->createExcel($sheets, $folder, 'syncthetic_' . date('d-m-Y-H-i-s') . '.xlsx'));
        });
    }

    private function createHeaderSynctheticExcel()
    {
        $customNames = $this->personnelCustomFieldService->getNames();
        $titles = array_merge(
            [
                'Tên nhân sự',
                'Số căn cước công dân',
            ],
            $customNames
        );

        $headerExcel = [
            array_map(fn($item) => [
                'name' => $item,
                'rowspan' => 1,
                'colspan' => 1,
            ], $titles),
        ];
        return $headerExcel;
    }

    private function createDataSynctheticExcel(array $records)
    {
        $data = array_map(function ($item) {
            return array_merge(
                [
                    $item['name'],
                    $item['citizen_identification_number'],
                ],
                array_map(function ($subItem) {
                    return $subItem['value'];
                }, $item['employment_contract_personnel_pivot_employment_contract_personnel_custom_field'] ?? []),
            );
        }, $records);

        return $data;
    }
}
