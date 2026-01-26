<?php

namespace App\Services;

use App\Repositories\ContractPersonnelRepository;
use Exception;

class ContractPersonnelService extends BaseService
{
    const FOLDER = 'uploads/render/contract/personnel';

    public function __construct(
        private ExcelService $excelService,
        private ContractService $contractService,
        private ContractInvestorService $contractInvestorService,
        private PersonnelService $personnelService,
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(ContractPersonnelRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['is_in_contract']))
            $array['is_in_contract'] = $this->repository->isInContract($array['is_in_contract']);
        return $array;
    }

    public function baseDataForLView()
    {
        $baseRequest = [
            'load_relations' => false,
        ];
        return [
            'personnels' => $this->personnelService->list($baseRequest),
            'investors' => $this->contractInvestorService->list($baseRequest),
            'years' => $this->contractService->getYears(),
            'isInContract' => $this->repository->isInContract(),
        ];
    }

    public function export(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $this->handlerUploadFileService->cleanupOldOverlapFiles(self::FOLDER);
            return asset($this->excelService->createExcel(
                [
                    (object) [
                        'name' => 'Nhân sự',
                        'header' => [
                            [
                                ...array_map(fn($i) => [
                                    'name' => $i,
                                    'row_span' => 1,
                                    'col_span' => 1,
                                ], [
                                    'Tên nhân sự',
                                    'ID',
                                    'Trình độ học vấn',
                                    'Đơn vị',
                                ])
                            ]
                        ],
                        'data' => collect($this->personnelService->list([
                            'columns' => [
                                'name',
                                'id',
                                'educational_level',
                                'personnel_unit_id',
                            ]
                        ]))->values()->map(fn($i) => array_values([
                            $i['name'],
                            $i['id'],
                            $i['educational_level'],
                            $i['personnel_unit']['name'],
                        ]))->toArray(),
                        'boldRows' => [1],
                        'boldItalicRows' => [],
                        'italicRows' => [],
                        'centerColumns' => [],
                        'centerRows' => [],
                        'filterStartRow' => 1,
                        'freezePane' => 'freezeTopRow',
                    ],
                    (object) [
                        'name' => 'data',
                        'header' => [
                            [
                                ...array_map(fn($i) => [
                                    'name' => $i,
                                    'row_span' => 1,
                                    'col_span' => 1,
                                ], [
                                    'ID Nhân Sự (Vlookup cột ID bên sheet danh sách nhân sự)',
                                    'Có trong hợp đồng (0 hoặc 1)',
                                    'Chức danh',
                                    'Chức danh (EN)',
                                    'Đơn vị huy động',
                                    'Đơn vị huy động (EN)',
                                    'Nhiệm vụ thực hiện trong hợp đồng',
                                    'Nhiệm vụ thực hiện trong hợp đồng (EN)',
                                ])
                            ]
                        ],
                        'data' => collect($this->repository->list([
                            'contract_id' => $request['contract_id'],
                            'columns' => [
                                'personnel_id',
                                'is_in_contract',
                                'position',
                                'position_en',
                                'mobilized_unit',
                                'mobilized_unit_en',
                                'task',
                                'task_en',
                            ]
                        ]))->map(fn($i) => array_values([
                            'personnel_id' => $i['personnel_id'],
                            'is_in_contract' => $i['is_in_contract'],
                            'position' => $i['position'],
                            'position_en' => $i['position_en'],
                            'mobilized_unit' => $i['mobilized_unit'],
                            'mobilized_unit_en' => $i['mobilized_unit_en'],
                            'task' => $i['task'],
                            'task_en' => $i['task_en'],
                        ]))->toArray(),
                        'boldRows' => [1],
                        'boldItalicRows' => [],
                        'italicRows' => [],
                        'centerColumns' => [],
                        'centerRows' => [],
                        'filterStartRow' => 1,
                        'freezePane' => 'freezeTopRow',
                    ],
                ],
                self::FOLDER,
                'contract_personnel_' . date('d-m-Y_H-i-s') . '.xlsx'
            ));
        });
    }

    public function import(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $rawData = array_slice(
                $this->excelService->readExcel($request['file'])['data'] ?? [],
                1
            );

            $authId = $this->getUserId();
            $errors = [];
            $insertData = [];

            foreach ($rawData as $index => $row) {
                $rowNumber = $index + 2;  // +2 vì bỏ header
                $personnelId = $row[0] ?? null;
                $isInContract = $row[1] ?? null;

                if (!$personnelId) {
                    $errors[] = "Dòng {$rowNumber}: ID nhân sự đang rỗng";
                    continue;
                }

                if (!$this->personnelService->findById($personnelId)) {
                    $errors[] = "Dòng {$rowNumber}: Không có nhân sự nào có id={$personnelId}";
                    continue;
                }

                if (!in_array((string) $isInContract, ['0', '1'], true)) {
                    $errors[] = "Dòng {$rowNumber}: Có trong hợp đồng phải là 0 hoặc 1 (id={$personnelId})";
                    continue;
                }

                $insertData[] = array_replace(
                    [
                        'contract_id' => $request['contract_id'],
                        'created_by' => $authId,
                    ],
                    array_combine(
                        [
                            'personnel_id',
                            'is_in_contract',
                            'position',
                            'position_en',
                            'mobilized_unit',
                            'mobilized_unit_en',
                            'task',
                            'task_en',
                        ],
                        array_pad($row, 8, null)
                    )
                );
            }

            if (!empty($errors))
                throw new Exception(implode("\n", $errors));

            $this->repository->deleteByContractId($request['contract_id']);
            $this->repository->upsert(
                $insertData,
                ['contract_id', 'personnel_id'],  // khóa unique
                [
                    'is_in_contract',
                    'position',
                    'position_en',
                    'mobilized_unit',
                    'mobilized_unit_en',
                    'task',
                    'task_en',
                    'updated_at',
                ]
            );
        }, true);
    }

    public function synthetic(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $this->handlerUploadFileService->cleanupOldOverlapFiles(self::FOLDER);
            return asset($this->excelService->createExcel(
                [
                    (object) [
                        'name' => 'data',
                        'header' => [
                            [
                                ...array_map(fn($i) => [
                                    'name' => $i,
                                    'row_span' => 1,
                                    'col_span' => 1,
                                ], [
                                    'Năm',
                                    'Số hợp đồng',
                                    'Tên hợp đồng',
                                    'Tên hợp đồng (EN)',
                                    'Địa điểm',
                                    'Chủ đầu tư',
                                    'Chủ đầu tư (EN)',
                                    'Giá trị hợp đồng',
                                    'Ngày ký hợp đồng',
                                    'Ngày nghiệm thu',
                                    'Có tên trong hợp đồng (có:1; không:0)',
                                    'Vị trí',
                                    'Đơn vị huy động',
                                    'Nhiệm vụ',
                                    'Người liên hệ/Chủ đầu tư',
                                    'Vị trí (EN)',
                                    'Đơn vị huy động (EN)',
                                    'Nhiệm vụ (EN)',
                                    'Người liên hệ/Chủ đầu tư (EN)',
                                ])
                            ]
                        ],
                        'data' => collect($this->repository->synthetic($request))->map(fn($i) => array_values([
                            $i['contract']['year'] ?? null,
                            $i['contract']['contract_number'] ?? null,
                            $i['contract']['name'] ?? null,
                            $i['contract']['name_en'] ?? null,
                            implode(', ', collect($i['contract']['scope'] ?? [])->map(fn($e) => $e['province']['name'] ?? null)->filter()->toArray()),
                            $i['contract']['investor']['name_vi'] ?? null,
                            $i['contract']['investor']['name_en'] ?? null,
                            $i['contract']['contract_value'] ?? null,
                            $this->formatDateForPreview($i['contract']['signed_date'] ?? null),
                            $this->formatDateForPreview($i['contract']['acceptance_date'] ?? null),
                            $i['is_in_contract'] ?? null,
                            $i['position'] ?? null,
                            $i['mobilized_unit'] ?? null,
                            $i['task'] ?? null,
                            $i['position_en'] ?? null,
                            $i['mobilized_unit_en'] ?? null,
                            $i['task_en'] ?? null,
                        ]))->toArray(),
                        'boldRows' => [1],
                        'boldItalicRows' => [],
                        'italicRows' => [],
                        'centerColumns' => [],
                        'centerRows' => [],
                        'filterStartRow' => 1,
                        'freezePane' => 'freezeTopRow',
                    ],
                ],
                self::FOLDER,
                'contract_personnel_synthetic_' . date('d-m-Y_H-i-s') . '.xlsx'
            ));
        });
    }
}
