<?php

namespace App\Services;

use App\Repositories\DossierPlanRepository;
use Illuminate\Support\Facades\Validator;
use Exception;

class DossierPlanService extends BaseService
{
    private $provinceService;
    private $communeService;
    private $unitService;
    private $dossierTypeService;
    private $excelService;
    private $dossierMinuteService;
    private $userService;
    private $planDetailService;

    public function __construct()
    {
        $this->repository = app(DossierPlanRepository::class);
        $this->provinceService = app(ProvinceService::class);
        $this->communeService = app(CommuneService::class);
        $this->unitService = app(UnitService::class);
        $this->dossierTypeService = app(DossierTypeService::class);
        $this->excelService = app(ExcelService::class);
        $this->dossierMinuteService = app(DossierMinuteService::class);
        $this->userService = app(UserService::class);
        $this->planDetailService = app(DossierPlanDetailService::class);
    }

    public function baseIndexData()
    {
        return $this->tryThrow(function () {
            return array_merge(
                app(DossierService::class)->baseIndexData(),
                [
                    'users' => $this->userService->list([
                        'load_relations' => false,
                        'columns' => ['id', 'name'],
                    ]),
                    'pageTitle' => 'Lập kế hoạch',
                ]
            );
        });
    }

    public function beforeImport(array $request)
    {
        return array_map([$this, 'beforeStore'], $request);
    }

    public function findByIdContractAndYear(int $idContract, int $year = null)
    {
        return $this->tryThrow(function () use ($idContract, $year) {
            $data = $this->repository->findByIdContractAndYear($idContract, $year);
            if (!empty($data))
                $data = $this->formatRecord($data->toArray());
            return $data;
        });
    }

    public function createTempExcel()
    {
        return $this->tryThrow(function () {
            {
                $dossierService = app(DossierService::class);

                $sheets = $dossierService->baseCreateTempExcel();
                $sheets[] = (object) [
                    'name' => 'data',
                    'header' => [
                        [
                            [
                                'name' => 'Loại biên bản (copy bên sheet loại biên bản)',
                                'rowspan' => 1,
                                'colspan' => 1,
                            ],
                            [
                                'name' => 'Tỉnh (copy bên sheet tỉnh)',
                                'rowspan' => 1,
                                'colspan' => 1,
                            ],
                            [
                                'name' => 'Xã (copy bên sheet tỉnh)',
                                'rowspan' => 1,
                                'colspan' => 1,
                            ],
                            [
                                'name' => 'Đơn vị (copy bên sheet đơn vị)',
                                'rowspan' => 1,
                                'colspan' => 1,
                            ],
                            [
                                'name' => 'Thời gian (set định dạng cột là TEXT và nhập YYYY-MM-DD)',
                                'rowspan' => 1,
                                'colspan' => 1,
                            ],
                            [
                                'name' => 'Người phụ trách (copy bên sheet nhân sự)',
                                'rowspan' => 1,
                                'colspan' => 1,
                            ],
                            [
                                'name' => 'Số lượng',
                                'rowspan' => 1,
                                'colspan' => 1,
                            ],
                            [
                                'name' => 'Ghi chú',
                                'rowspan' => 1,
                                'colspan' => 1,
                            ],
                        ]
                    ],
                    'data' => [],
                    'boldRows' => [1],
                    'boldItalicRows' => [],
                    'italicRows' => [],
                    'centerColumns' => [],
                    'centerRows' => [],
                    'filterStartRow' => 1,
                    'freezePane' => 'freezeTopRow',
                ];

                return asset($dossierService->createExcel(
                    'uploads/dossier/plan',
                    uniqid('dossier_plan') . '.xlsx',
                    $sheets
                ));
            }
        });
    }

    public function uploadExcel(int $contractId, $file)
    {
        return $this->tryThrow(function () use ($contractId, $file) {
            $plan = $this->repository->findByIdContractAndYear($contractId, null);
            if (empty($plan)) {
                $plan = $this->repository->store([
                    'contract_id' => $contractId,
                    'user_id' => auth()->id()
                ]);
            } else {
                $plan->update([
                    'user_id' => auth()->id()
                ]);
            }

            $lastMinute = $this->getLastMinute($plan);
            $this->dossierMinuteService->validateMinuteStatusWhenCreate($lastMinute);

            $data = $this->excelService->readExcel($file);
            if (!isset($data['data'])) {
                throw new Exception('File excel bị mất sheet "data" ban đầu - vui lòng giữ nguyên tên sheet!');
            }

            $sheetData = $data['data'];
            unset($sheetData[0]);
            $validateExcel = $this->validateExcelData(array_values($sheetData));

            $plan->details()->delete();
            $plan->details()->createMany($validateExcel);
            return $plan;
        }, true);
    }

    private function validateExcelRow(array $data, int $rowIndex = 0)
    {
        // Map array to named fields
        $rowData = [
            'dossier_type' => $data[0] ?? null,
            'province_name' => $data[1] ?? null,
            'commune_name' => $data[2] ?? null,
            'unit_name' => $data[3] ?? null,
            'estimated_time' => $data[4] ?? null,
            'responsible_user_name' => $data[5] ?? null,
            'quantity' => $data[6] ?? null,
            'note' => $data[7] ?? null,
        ];

        // Validation rules using Laravel exists
        $rules = [
            'dossier_type' => 'required|string|exists:dossier_types,name',
            'province_name' => 'required|string|exists:provinces,name',
            'commune_name' => 'nullable|string|exists:communes,name',
            'unit_name' => 'nullable|string|exists:units,name',
            'estimated_time' => 'required|date_format:Y-m-d',
            'responsible_user_name' => 'required|string|exists:users,name',
            'quantity' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:255'
        ];

        // Custom messages
        $messages = [
            // Dossier type messages
            'dossier_type.required' => 'Loại biên bản là bắt buộc.',
            'dossier_type.string' => 'Loại biên bản phải là chuỗi ký tự.',
            'dossier_type.exists' => 'Loại biên bản ":input" không tồn tại trong hệ thống.',
            // Province messages
            'province_name.required' => 'Tên tỉnh là bắt buộc.',
            'province_name.string' => 'Tên tỉnh phải là chuỗi ký tự.',
            'province_name.exists' => 'Tỉnh ":input" không tồn tại trong hệ thống.',
            // Commune messages
            'commune_name.string' => 'Tên xã phải là chuỗi ký tự.',
            'commune_name.exists' => 'Xã ":input" không tồn tại trong hệ thống.',
            // Unit messages
            'unit_name.string' => 'Tên đơn vị phải là chuỗi ký tự.',
            'unit_name.exists' => 'Đơn vị ":input" không tồn tại trong hệ thống.',
            // Estimated time messages
            'estimated_time.required' => 'Thời gian là bắt buộc.',
            'estimated_time.date_format' => 'Thời gian phải có định dạng YYYY-MM-DD (ví dụ: 2025-08-18).',
            // Responsible user messages
            'responsible_user_name.required' => 'Người phụ trách là bắt buộc.',
            'responsible_user_name.string' => 'Tên người phụ trách phải là chuỗi ký tự.',
            'responsible_user_name.exists' => 'Người phụ trách ":input" không tồn tại trong hệ thống.',
            // Quantity messages
            'quantity.required' => 'Số lượng là bắt buộc.',
            'quantity.numeric' => 'Số lượng phải là số.',
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
            // Note messages
            'note.string' => 'Ghi chú phải là chuỗi ký tự.',
            'note.max' => 'Ghi chú không được vượt quá 255 ký tự.'
        ];

        // Validate
        $validator = Validator::make($rowData, $rules, $messages);

        // Custom validation: phải có ít nhất commune HOẶC unit
        $validator->after(function ($validator) use ($rowData) {
            if (empty($rowData['commune_name']) && empty($rowData['unit_name'])) {
                $validator->errors()->add('commune_name', 'Phải có ít nhất một trong hai: Tên xã hoặc Tên đơn vị.');
            }
        });

        if ($validator->fails()) {
            throw new Exception('Lỗi tại dòng ' . ($rowIndex + 1) . ': ' . implode(', ', $validator->errors()->all()));
        }

        return $rowData;  // Return validated data (still names, not IDs)
    }

    private function convertNamesToIds(array $validatedRows)
    {
        // Collect unique names
        $uniqueDossierTypes = array_unique(array_filter(array_column($validatedRows, 'dossier_type')));
        $uniqueProvinces = array_unique(array_filter(array_column($validatedRows, 'province_name')));
        $uniqueCommunes = array_unique(array_filter(array_column($validatedRows, 'commune_name')));
        $uniqueUnits = array_unique(array_filter(array_column($validatedRows, 'unit_name')));
        $uniqueUsers = array_unique(array_filter(array_column($validatedRows, 'responsible_user_name')));

        // Get data from services
        $dossierTypesData = $this->dossierTypeService->findByNames($uniqueDossierTypes)->toArray();
        $provincesData = $this->provinceService->findByNames($uniqueProvinces)->toArray();
        $communesData = $this->communeService->findByNames($uniqueCommunes)->toArray();
        $unitsData = $this->unitService->findByNames($uniqueUnits)->toArray();
        $usersData = $this->userService->findByNames($uniqueUsers)->toArray();

        // Helper functions to find IDs
        $getDossierTypeId = function ($name) use ($dossierTypesData) {
            foreach ($dossierTypesData as $item) {
                if ($item['name'] === $name) {
                    return $item['id'];  // Dùng id từ dossier_types
                }
            }
            return null;
        };

        $getProvinceId = function ($name) use ($provincesData) {
            foreach ($provincesData as $item) {
                if ($item['name'] === $name) {
                    return $item['code'];  // Dùng code làm ID
                }
            }
            return null;
        };

        $getCommuneId = function ($communeName, $provinceName) use ($communesData, $provincesData) {
            // Tìm province_code trước
            $provinceCode = null;
            foreach ($provincesData as $province) {
                if ($province['name'] === $provinceName) {
                    $provinceCode = $province['code'];
                    break;
                }
            }

            if (!$provinceCode)
                return null;

            // Tìm commune thuộc đúng province
            foreach ($communesData as $commune) {
                if ($commune['name'] === $communeName && $commune['province_code'] === $provinceCode) {
                    return $commune['code'];
                }
            }
            return null;
        };

        $getUnitId = function ($name) use ($unitsData) {
            foreach ($unitsData as $unit) {
                if ($unit['name'] === $name) {
                    return $unit['id'];
                }
            }
            return null;
        };

        $getUserId = function ($name) use ($usersData) {
            foreach ($usersData as $user) {
                if ($user['name'] === $name) {
                    return $user['id'];
                }
            }
            return null;
        };

        // Convert each row
        return array_map(function ($row) use ($getDossierTypeId, $getProvinceId, $getCommuneId, $getUnitId, $getUserId) {
            return [
                'dossier_type_id' => $getDossierTypeId($row['dossier_type']),
                'province_code' => $getProvinceId($row['province_name']),
                'commune_code' => $row['commune_name'] ? $getCommuneId($row['commune_name'], $row['province_name']) : null,
                'unit_id' => $row['unit_name'] ? $getUnitId($row['unit_name']) : null,
                'estimated_time' => $row['estimated_time'],
                'responsible_user_id' => $getUserId($row['responsible_user_name']),
                'quantity' => (int) $row['quantity'],
                'note' => $row['note']
            ];
        }, $validatedRows);
    }

    private function validateExcelData(array $excelData)
    {
        $validatedRows = [];
        $errors = [];

        foreach ($excelData as $index => $row) {
            try {
                $validatedRow = $this->validateExcelRow($row, $index);
                $validatedRows[] = $validatedRow;
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode("\n", $errors));
        }

        // Convert names to IDs after validation
        $dataWithIds = $this->convertNamesToIds($validatedRows);
        if (empty($dataWithIds)) {
            throw new Exception('Không có dữ liệu hợp lệ nào để lưu.');
        }

        return $dataWithIds;
    }

    public function createMinute(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $minute = $this->dossierMinuteService->createPlanMinute($request);
            return $minute;
        });
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        if (isset($array['handover_date']))
            $array['handover_date'] = $this->formatDateForPreview($array['handover_date']);

        if (isset($array['minutes']))
            $array['minutes'] = $this->dossierMinuteService->formatRecords($array['minutes']);

        if (isset($array['details']))
            $array['details'] = $this->planDetailService->formatRecords($array['details']);

        return $array;
    }

    public function sendApproveRequest(int $contractId)
    {
        return $this->tryThrow(function () use ($contractId) {
            $plan = $this->repository->findByIdContractAndYear($contractId, null);
            if (empty($plan)) {
                throw new Exception('Kế hoạch không tồn tại');
            }

            $lastMinute = $this->getLastMinute($plan);
            $this->dossierMinuteService->validateMinuteStatusWhenSendApproveRequest($lastMinute);

            $lastMinute->update(['status' => 'pending_approval']);

            $this->dossierMinuteService->sendMail(
                'Yêu cầu phê duyệt bàn giao kế hoạch chứng từ hồ sơ ngoại nghiệp',
                [
                    'name' => $plan->contract->name,
                    'type' => 0,
                    'ghichu' => ''
                ],
                $lastMinute
            );
        }, true);
    }

    public function getLastMinute($plan)
    {
        return $plan->minutes()->latest()->first();
    }
}
