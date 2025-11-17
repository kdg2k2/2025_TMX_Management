<?php
namespace App\Services;

use App\Models\DossierUsageRegister;
use App\Repositories\DossierUsageRegisterRepository;
use App\Services\DossierMinuteService;
use App\Services\DossierPlanService;
use Exception;

class DossierUsageRegisterService extends BaseService
{
    private $dossierHandoverService;
    private $dossierMinuteService;
    private $dossierPlanService;

    public function __construct()
    {
        $this->repository = app(DossierUsageRegisterRepository::class);
        $this->dossierHandoverService = app(DossierHandoverService::class);
        $this->dossierMinuteService = app(DossierMinuteService::class);
        $this->dossierPlanService = app(DossierPlanService::class);
    }

    public function findByIdContractAndYear($contractId, $year)
    {
        return $this->tryThrow(function () use ($contractId, $year) {
            $available = $this->getAvailable($contractId, $year);  // hiện trạng
            $register = $this->getRegistersInDraftOrPendingApproval($contractId, $year) ?? [];  // đang đăng ký

            $registerMerged = $this->mergeRegistersIntoAvailable($available, $this->createBaseFormData([$register], true));  // merge hiện trạng và đang đăng ký

            $minute = $this->getMinuteById($register['id'] ?? null);
            return [
                'registerMerged' => array_values($registerMerged),
                'flag' => [
                    'user' => optional($register)->registeredBy,
                    'has_data' => !empty($register),
                    'minutes' => $minute ? [$this->dossierMinuteService->formatRecord($minute)] : [],
                ],
            ];
        });
    }

    private function getMinuteById(int $id = null)
    {
        if (empty($id)) {
            return null;
        }
        $register = $this->repository->findById($id);
        $minute = $register ? (optional($this->getLastMinute($register))->toArray() ?? []) : null;
        return $minute;
    }

    /**
     * Merge dữ liệu đăng ký vào hiện trạng
     * - Thêm cột số lượng đăng ký
     * - Ghi đè note nếu có
     */
    private function mergeRegistersIntoAvailable(array $available, array $registers): array
    {
        foreach ($available as $key => &$row) {
            // mặc định cột 7 = số lượng đăng ký = 0
            $row[7] = 0;

            if (isset($registers[$key])) {
                $row[7] = $registers[$key][4];  // số lượng đăng ký
                if (!empty($registers[$key][6])) {
                    $row[6] = $registers[$key][6];  // ghi đè note
                }
            }
        }
        unset($row);

        // xử lý trường hợp đăng ký có mà available không có
        foreach ($registers as $key => $row) {
            if (!isset($available[$key])) {
                $row[7] = $row[4];  // tất cả là số lượng đăng ký
                $row[4] = 0;  // khả dụng = 0
                $available[$key] = $row;
            }
        }

        return $available;
    }

    /**
     * Gom dữ liệu chi tiết, gộp theo key unique và cộng dồn số lượng
     */
    private function createBaseFormData($arrayHasDetail, bool $takeNote = false)
    {
        $data = [];
        if (!empty($arrayHasDetail)) {
            $data = array_merge(...array_map(function ($array) use ($takeNote) {
                return array_map(function ($item) use ($takeNote) {
                    return [
                        $item['type']['name'] ?? null,
                        $item['province']['name'] ?? null,
                        $item['commune']['name'] ?? null,
                        $item['unit']['name'] ?? null,
                        $item['quantity'] ?? 0,
                        $item['type']['unit'],
                        ($takeNote ? $item['note'] : null) ?? null,
                    ];
                }, $array['details'] ?? []);
            }, $arrayHasDetail));
        }

        $merged = [];
        foreach ($data as $row) {
            $key = $this->makeUniqueKey($row);
            if (!isset($merged[$key])) {
                $merged[$key] = $row;
            } else {
                $merged[$key][4] += $row[4];  // cột số lượng
            }
        }
        return $merged;
    }

    public function getAvailable(int $contractId, int $year = null)
    {
        // Lấy tổng kho và đã sử dụng để tính hiện trạng
        $total = $this->createBaseFormData(
            $this->dossierHandoverService->getHandoverInsApproved($contractId, $year) ?? []
        );  // tổng kho
        $used = $this->createBaseFormData(
            $this->getRegistersApproved($contractId, $year) ?? []
        );  // đã sử dụng
        $available = $this->calculateAvailable($total, $used);  // hiện trạng

        return $available;
    }

    /**
     * Tính available = total - used
     */
    private function calculateAvailable(array $total, array $used): array
    {
        foreach ($used as $key => $row) {
            if (isset($total[$key])) {
                $total[$key][4] -= $row[4];
                if ($total[$key][4] <= 0) {
                    unset($total[$key]);
                }
            }
        }
        return $total;
    }

    public function getRegistersInDraftOrPendingApproval($contractId, $year)
    {
        $data = $this->repository->list([
            'contract_id' => $contractId,
            'year' => $year,
            'minute_status' => ['draft', 'pending_approval'],
        ]);
        if (!empty($data))
            $data = reset($data);
        return $data ?? null;
    }

    public function getRegistersApproved($contractId, $year)
    {
        return $this->repository->list([
            'contract_id' => $contractId,
            'year' => $year,
            'minute_status' => 'approved',
        ]);
    }

    /**
     * Tạo key unique từ một row
     */
    private function makeUniqueKey(array $row): string
    {
        return implode('|', [$row[0], $row[1], $row[2], $row[3]]);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        if (isset($array['minutes']))
            $array['minutes'] = $this->dossierMinuteService->formatRecords($array['minutes']);

        if (isset($array['plan']))
            $array['plan'] = $this->dossierPlanService->formatRecord($array['plan']);

        return $array;
    }

    private function getAvailableDataForCreateExcel(int $contractId, int $year = null)
    {
        $data = array_map(function ($item) {
            array_splice($item, 5, 0, [null]);
            return $item;
        }, array_values(
            $this->getAvailable($contractId, $year ?? null) ?? []
        ));

        return $data;
    }

    public function createTempExcel(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $dossierService = app(DossierService::class);

            $data = $this->getAvailableDataForCreateExcel($request['contract_id'], $request['year'] ?? null);

            $sheets = [
                (object) [
                    'name' => 'data',
                    'header' => [
                        [
                            [
                                'name' => 'Loại biên bản',
                                'rowspan' => 1,
                                'colspan' => 1,
                            ],
                            [
                                'name' => 'Tỉnh',
                                'rowspan' => 1,
                                'colspan' => 1,
                            ],
                            [
                                'name' => 'Xã',
                                'rowspan' => 1,
                                'colspan' => 1,
                            ],
                            [
                                'name' => 'Đơn vị',
                                'rowspan' => 1,
                                'colspan' => 1,
                            ],
                            [
                                'name' => 'Số lượng khả dụng',
                                'rowspan' => 1,
                                'colspan' => 1,
                            ],
                            [
                                'name' => 'Số lượng',
                                'rowspan' => 1,
                                'colspan' => 1,
                            ],
                            [
                                'name' => 'Đơn vị tính',
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
                    'data' => $data,
                    'boldRows' => [1],
                    'boldItalicRows' => [],
                    'italicRows' => [],
                    'centerColumns' => [],
                    'centerRows' => [],
                    'filterStartRow' => 1,
                    'freezePane' => 'freezeTopRow',
                ]
            ];

            return asset($dossierService->createExcel(
                'dossier/usage_register',
                uniqid('dossier_usage_register') . '.xlsx',
                $sheets
            ));
        });
    }

    public function getLastMinute(DossierUsageRegister $record)
    {
        return $record->minutes()->latest()->first();
    }

    public function uploadExcel(int $contractId, $file)
    {
        return $this->tryThrow(function () use ($contractId, $file) {
            $data = app(ExcelService::class)->readExcel($file);
            if (!isset($data['data']))
                throw new Exception('File excel bị mất sheet "data" ban đầu - vui lòng giữ nguyên tên sheet!');

            $originData = $this->getAvailableDataForCreateExcel($contractId, null);
            $sheetData = $data['data'];
            unset($sheetData[0]);  // bỏ header

            // Validate dữ liệu từ excel
            $validated = $this->validateExcelData($originData, $sheetData);

            if (!empty($validated['errors'])) {
                throw new Exception(implode("\n", $validated['errors']));
            }

            $this->repository->deleteByContractIdAndMinuteStatus([
                'contract_id' => $contractId,
                'minute_status' => 'draft',
            ]);

            $usageRegister = $this->repository->store([
                'dossier_plan_id' => $this->getPlanMinute($contractId)->plan->id,
                'registered_by' => auth()->id(),
            ]);

            $validated = array_map(function ($item) use ($usageRegister) {
                $item['dossier_usage_register_id'] = $usageRegister['id'];
                return $item;
            }, $validated['data']);

            $usageRegister->details()->createMany($validated);

            $lastMinute = $this->getMinuteById($usageRegister['id']);
            $this->dossierMinuteService->validateMinuteStatusWhenCreate(empty($lastMinute) ? null : $lastMinute);

            $usageRegister = $this->repository->findById($usageRegister['id']);
            $this->dossierMinuteService->createUsageRegisterMinute($usageRegister->toArray(), $file);
        }, true);
    }

    private function getPlanMinute(int $contractId)
    {
        $planMinute = $this->dossierMinuteService->findByContractId($contractId);
        if (empty($planMinute))
            throw new Exception('Hợp đồng này còn chưa có biên bản kế hoạch nên không đăng ký sử dụng được');
        return $planMinute;
    }

    private function validateExcelData(array $originData, array $sheetData)
    {
        $errors = [];
        $validatedData = [];

        $getColumnName = function ($columnIndex) {
            $columnNames = [
                0 => 'Loại biên bản',
                1 => 'Tỉnh',
                2 => 'Xã',
                3 => 'Đơn vị',
                4 => 'Số lượng tối đa',
                5 => 'Số lượng đăng ký',
                6 => 'Đơn vị tính',
                7 => 'Ghi chú'
            ];

            return $columnNames[$columnIndex] ?? "Cột {$columnIndex}";
        };

        // Kiểm tra số lượng records
        if (count($originData) !== count($sheetData)) {
            $errors[] = 'Số lượng dòng dữ liệu không khớp với template gốc';
            return ['errors' => $errors, 'data' => []];
        }

        // Tạo map các service để convert
        $dossierTypeService = app(DossierTypeService::class);
        $provinceService = app(ProvinceService::class);
        $communeService = app(CommuneService::class);
        $unitService = app(UnitService::class);

        $sheetDataReindexed = array_values($sheetData);  // Reindex để bắt đầu từ 0

        foreach ($originData as $index => $originRow) {
            $sheetRow = $sheetDataReindexed[$index] ?? null;
            $rowNumber = $index + 2;  // +2 vì có header và index bắt đầu từ 0

            if (!$sheetRow) {
                $errors[] = "Dòng {$rowNumber}: Thiếu dữ liệu";
                continue;
            }

            // Validate các cột không được thay đổi (0, 1, 2, 3, 4, 6)
            $immutableColumns = [0, 1, 2, 3, 4, 6];
            foreach ($immutableColumns as $col) {
                $originValue = $originRow[$col];
                $sheetValue = $sheetRow[$col];

                // Convert cột 4 về cùng kiểu để so sánh
                if ($col === 4) {
                    $originValue = (string) $originValue;
                    $sheetValue = (string) $sheetValue;
                }

                if ($originValue !== $sheetValue) {
                    $columnName = $getColumnName($col);
                    $errors[] = "Dòng {$rowNumber}, cột {$columnName}: Không được thay đổi giá trị ('{$originValue}' → '{$sheetValue}')";
                }
            }

            // Convert và validate dữ liệu
            $convertedRow = $this->convertExcelRow($sheetRow, $dossierTypeService, $provinceService, $communeService, $unitService, $rowNumber, $errors);

            // Validate cột 5 (số lượng đăng ký)
            $quantity = $sheetRow[5] ?? null;
            $maxQuantity = (int) $originRow[4];

            if ($quantity !== null) {
                if (!is_numeric($quantity) || !is_int((int) $quantity) || (int) $quantity <= 0) {
                    $errors[] = "Dòng {$rowNumber}, cột 'Số lượng đăng ký': Phải là số nguyên dương";
                } elseif ((int) $quantity > $maxQuantity) {
                    $errors[] = "Dòng {$rowNumber}, cột 'Số lượng đăng ký': Không được vượt quá {$maxQuantity}";
                }
            }

            // Validate cột 7 (ghi chú)
            $note = $sheetRow[7] ?? null;
            if ($note !== null && strlen($note) > 255) {
                $errors[] = "Dòng {$rowNumber}, cột 'Ghi chú': Không được quá 255 ký tự";
            }

            $validatedData[] = array_merge($convertedRow, [
                'quantity' => $quantity ? (int) $quantity : null,
                'note' => $note,
            ]);
        }

        $hasAnyQuantity = false;
        foreach ($validatedData as $row) {
            if ($row['quantity'] !== null && $row['quantity'] > 0) {
                $hasAnyQuantity = true;
                break;
            }
        }

        if (!$hasAnyQuantity)
            $errors[] = 'Không có dữ liệu bàn giao nào được nhập (tất cả số lượng đăng ký đều trống)';

        return [
            'errors' => $errors,
            'data' => $validatedData
        ];
    }

    private function convertExcelRow($row, $dossierTypeService, $provinceService, $communeService, $unitService, $rowNumber, &$errors)
    {
        $converted = [];

        // Convert cột 0: Loại biên bản → ID
        $dossierType = optional($dossierTypeService->findByName($row[0]))->id ?? null;
        if (!$dossierType && !empty($row[0])) {
            $errors[] = "Dòng {$rowNumber}, cột 'Loại biên bản': Không tìm thấy '{$row[0]}'";
        }
        $converted['dossier_type_id'] = $dossierType;

        // Convert cột 1: Tỉnh → Code
        $provinceCode = optional($provinceService->findByName($row[1]))->code ?? null;
        if (!$provinceCode && !empty($row[1])) {
            $errors[] = "Dòng {$rowNumber}, cột 'Tỉnh': Không tìm thấy '{$row[1]}'";
        }
        $converted['province_code'] = $provinceCode;

        // Convert cột 2: Xã → Code
        $communeCode = null;
        if (!empty($row[2])) {
            $commune = $communeService->findByName($row[2]);
            $communeCode = optional($commune)->code ?? null;
            if (!$communeCode) {
                $errors[] = "Dòng {$rowNumber}, cột 'Xã': Không tìm thấy '{$row[2]}'";
            }
        }
        $converted['commune_code'] = $communeCode;

        // Convert cột 3: Đơn vị → ID
        $unitId = null;
        if (!empty($row[3])) {
            $unit = $unitService->findByName($row[3]);
            $unitId = optional($unit)->id ?? null;
            if (!$unitId) {
                $errors[] = "Dòng {$rowNumber}, cột 'Đơn vị': Không tìm thấy '{$row[3]}'";
            }
        }
        $converted['unit_id'] = $unitId;

        return $converted;
    }

    public function sendApproveRequest(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $register = $this->repository->findByContractIdAndMinuteStatus($request['contract_id'], 'draft');
            if (empty($register))
                throw new Exception('Phải upload dữ liệu lên trước!');
            $minute = $this->getLastMinute($register);
            $this->dossierMinuteService->validateMinuteStatusWhenCreate($minute);

            $register->update([
                'registered_by' => auth()->id(),
                'handover_date' => $request['handover_date'],
            ]);

            $minute->update([
                'status' => 'pending_approval',
            ]);

            $this->dossierMinuteService->sendMail(
                'Yêu cầu phê duyệt đăng ký sử dụng chứng từ hồ sơ ngoại nghiệp',
                ['type' => 6, 'name' => auth()->user()->name, 'sd_cho' => $register->plan->contract->name, 'ngaybangiao' => $request['handover_date']],
                $minute
            );
        });
    }
}
