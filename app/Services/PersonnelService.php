<?php

namespace App\Services;

use App\Models\Personnel;
use App\Repositories\PersonnelRepository;
use Exception;

class PersonnelService extends BaseService
{
    public function __construct(
        private PersonnelUnitService $personnelUnitService,
        private PersonnelCustomFieldService $personnelCustomFieldService,
        private ExcelService $excelService
    ) {
        $this->repository = app(PersonnelRepository::class);
    }

    public function getCreateOrUpdateBaseData(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->findById($id, true, true);

        $res['personnelUnits'] = $this->personnelUnitService->list();
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

    private function handleExtracted(Personnel $data, array $extracted, bool $isUpdate = false)
    {
        $extracted = $this->formatFields($data, $extracted);
        $this->personnelPivotPersonnelCustomField($data, $extracted);
    }

    private function formatFields(Personnel $data, array $fields)
    {
        $res = [];
        foreach ($fields as $key => $value) {
            $personnelCustomFieldId = optional($this->personnelCustomFieldService->findByField($key))->id ?? null;
            if (!$personnelCustomFieldId)
                throw new Exception('Không tìm thấy field');

            $res[] = [
                'personnel_id' => $data['id'],
                'personnel_custom_field_id' => $personnelCustomFieldId,
                'value' => $value,
            ];
        }
        return $res;
    }

    private function personnelPivotPersonnelCustomField(Personnel $data, array $pivot)
    {
        $data->personnelPivotPersonnelCustomField()->delete();

        if (count($pivot) == 0)
            return;

        $data->personnelPivotPersonnelCustomField()->insert($pivot);
    }

    public function synctheticExcel(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $records = $this->list($request);

            $header = $this->createHeaderSynctheticExcel();
            $data = $this->createDataSynctheticExcel($records ?? []);

            $sheets = [
                (object) [
                    'name' => 'personnels',
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

            return $this->getAssetUrl($this->excelService->createExcel($sheets, 'uploads/render', 'PersonnelSyncthetic_' . date('d-m-Y-H-i-s') . '.xlsx'));
        });
    }

    private function createHeaderSynctheticExcel()
    {
        $customNames = $this->personnelCustomFieldService->getNames();
        $titles = array_merge(
            [
                'Tên nhân sự',
                'Đơn vị',
                'Trình độ học vấn',
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
                    implode(' - ', array_filter([
                        $item['personnel_unit']['short_name'] ?? null,
                        $item['personnel_unit']['name'] ?? null,
                    ], fn($item) => !empty($item))),
                    $item['educational_level'],
                ],
                array_map(function ($subItem) {
                    return $subItem['value'];
                }, $item['personnel_pivot_personnel_custom_field'] ?? []),
            );
        }, $records);

        return $data;
    }
}
