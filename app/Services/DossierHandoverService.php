<?php
namespace App\Services;

use App\Models\DossierHandover;
use App\Repositories\DossierHandoverRepository;
use Exception;

class DossierHandoverService extends BaseService
{
    private $provinceService;
    private $dossierTypeService;
    private $dossierMinuteService;

    public function __construct()
    {
        $this->repository = app(DossierHandoverRepository::class);
        $this->provinceService = app(ProvinceService::class);
        $this->dossierTypeService = app(DossierTypeService::class);
        $this->dossierMinuteService = app(DossierMinuteService::class);
    }

    public function beforeStore(array $request)
    {
        $request['user_id'] = $this->getUserId();
        return $request;
    }

    public function beforeImport(array $request)
    {
        return array_map([$this, 'beforeStore'], $request);
    }

    public function deleteByPlanId(int $id)
    {
        return $this->tryThrow(function () use ($id) {
            return $this->repository->deleteByPlanId($id);
        }, true);
    }

    public function findByIdContractAndYear(int $contractId, int $year = null)
    {
        return $this->tryThrow(function () use ($contractId, $year) {
            $handoverOut = $this->getHandoverOut($contractId, $year);
            $handoverInDraftOrPendingApproval = $this->getHandoverInDraftOrPendingApproval($contractId, $year) ?? [];
            $handoverInsApproved = $this->getHandoverInsApproved($contractId, $year) ?? [];

            // dd([
            //     'số lượng giấy tờ theo kế hoạch cần được bàn giao' => $handoverOut,
            //     'số lượng giấy tờ bàn giao vào đang chờ duyệt hoặc nháp' => $handoverInDraftOrPendingApproval,
            //     'số lượng giấy tờ bàn giao vào đã được duyệt' => $handoverInsApproved
            // ]);

            $comparison = $this->mergeAndCompareHandovers($handoverOut, $handoverInDraftOrPendingApproval, $handoverInsApproved);

            $handoverInDraftOrPendingApproval = $this->formatRecord($handoverInDraftOrPendingApproval);
            return [
                'comparison' => $comparison,
                'flag' => [
                    'times' => $handoverInDraftOrPendingApproval['times'] ?? 0,
                    'has_data' => isset($handoverInDraftOrPendingApproval['details']) && count($handoverInDraftOrPendingApproval['details']) > 0,
                    'minutes' => $handoverInDraftOrPendingApproval['minutes'] ?? null,
                    'user' => $handoverInDraftOrPendingApproval['user'] ?? null,
                ],
            ];
        });
    }

    private function mergeAndCompareHandovers($keHoach, $dangChoduyet, $daDuyet)
    {
        $createKey = function ($item) {
            return implode('_', [
                $item['dossier_type_id'] ?? '',
                $item['province_code'] ?? '',
                $item['commune_code'] ?? '',
                $item['unit_id'] ?? 'null'
            ]);
        };

        // Lấy mảng details từ các tham số
        $keHoachDetails = isset($keHoach['details']) ? $keHoach['details'] : [];
        $dangChoDuyetDetails = isset($dangChoduyet['details']) ? $dangChoduyet['details'] : [];

        // Xử lý mảng đã duyệt (có thể có nhiều record)
        $daDuyetDetails = [];
        $dangChoDuyetId = $dangChoduyet['id'] ?? null;

        if (is_array($daDuyet)) {
            foreach ($daDuyet as $item) {
                if (isset($item['details']) && is_array($item['details'])) {
                    $handoverId = $item['id'] ?? null;

                    // Kiểm tra xem handover này có trùng với đang chờ duyệt không
                    if ($handoverId && $dangChoDuyetId && $handoverId == $dangChoDuyetId) {
                        // Bỏ qua nếu cùng ID với đang chờ duyệt (tránh trùng lặp)
                        continue;
                    }

                    $daDuyetDetails = array_merge($daDuyetDetails, $item['details']);
                }
            }
        }

        // Tạo mảng kết quả
        $result = [];

        // Xử lý kế hoạch
        foreach ($keHoachDetails as $item) {
            $key = $createKey($item);
            $result[$key] = [
                'dossier_type_id' => $item['dossier_type_id'],
                'province_code' => $item['province_code'],
                'commune_code' => $item['commune_code'],
                'unit_id' => $item['unit_id'],
                'type' => $item['type'] ?? null,
                // Thông tin địa chỉ từ kế hoạch
                'plan_province' => $item['province'] ?? null,
                'plan_commune' => $item['commune'] ?? null,
                'plan_unit' => $item['unit'] ?? null,
                // Thông tin địa chỉ từ bàn giao (sẽ được cập nhật sau)
                'handover_province' => null,
                'handover_commune' => null,
                'handover_unit' => null,
                'ke_hoach' => $item['quantity'],
                'dang_cho_duyet' => 0,
                'da_duyet' => 0,
                'ngoai_ke_hoach' => false,
                'ghi_chu' => []
            ];
        }

        // Xử lý đang chờ duyệt
        foreach ($dangChoDuyetDetails as $item) {
            $key = $createKey($item);

            if (!isset($result[$key])) {
                // Nếu không có trong kế hoạch -> tạo mới và đánh dấu ngoài kế hoạch
                $result[$key] = [
                    'dossier_type_id' => $item['dossier_type_id'],
                    'province_code' => $item['province_code'],
                    'commune_code' => $item['commune_code'],
                    'unit_id' => $item['unit_id'],
                    'type' => $item['type'] ?? null,
                    // Không có thông tin kế hoạch
                    'plan_province' => null,
                    'plan_commune' => null,
                    'plan_unit' => null,
                    // Thông tin địa chỉ từ bàn giao
                    'handover_province' => $item['province'] ?? null,
                    'handover_commune' => $item['commune'] ?? null,
                    'handover_unit' => $item['unit'] ?? null,
                    'ke_hoach' => 0,
                    'dang_cho_duyet' => 0,
                    'da_duyet' => 0,
                    'ngoai_ke_hoach' => true,
                    'ghi_chu' => []
                ];
            }

            $result[$key]['dang_cho_duyet'] += $item['quantity'];

            // Thêm ghi chú nếu có
            if (!empty($item['note'])) {
                $result[$key]['ghi_chu'][] = $item['note'];
            }
        }

        // Xử lý đã duyệt
        foreach ($daDuyetDetails as $item) {
            $key = $createKey($item);

            if (!isset($result[$key])) {
                // Nếu không có trong kế hoạch -> tạo mới và đánh dấu ngoài kế hoạch
                $result[$key] = [
                    'dossier_type_id' => $item['dossier_type_id'],
                    'province_code' => $item['province_code'],
                    'commune_code' => $item['commune_code'],
                    'unit_id' => $item['unit_id'],
                    'type' => $item['type'] ?? null,
                    // Không có thông tin kế hoạch
                    'plan_province' => null,
                    'plan_commune' => null,
                    'plan_unit' => null,
                    // Thông tin địa chỉ từ bàn giao
                    'handover_province' => $item['province'] ?? null,
                    'handover_commune' => $item['commune'] ?? null,
                    'handover_unit' => $item['unit'] ?? null,
                    'ke_hoach' => 0,
                    'dang_cho_duyet' => 0,
                    'da_duyet' => 0,
                    'ngoai_ke_hoach' => true,
                    'ghi_chu' => []
                ];
            }

            $result[$key]['da_duyet'] += $item['quantity'];

            // Thêm ghi chú nếu có
            if (!empty($item['note'])) {
                $result[$key]['ghi_chu'][] = $item['note'];
            }
        }

        // Tính toán thêm các chỉ số và xử lý ghi chú
        foreach ($result as &$row) {
            $row['tong_da_ban_giao'] = $row['da_duyet'] + $row['dang_cho_duyet'];

            // Tính còn lại (luôn >= 0)
            $row['con_lai'] = max(0, $row['ke_hoach'] - $row['da_duyet']);

            // Gộp các ghi chú thành chuỗi (loại bỏ trùng lặp)
            if (!empty($row['ghi_chu']))
                $row['ghi_chu'] = !empty($row['ghi_chu'])
                    // ? implode('; ', array_unique($row['ghi_chu'])) // nối chuỗi các note
                    ? reset($row['ghi_chu'])  // lấy note cuối cùng
                    : '';
        }
        unset($row);

        // Sắp xếp: trong kế hoạch trước, ngoài kế hoạch sau
        usort($result, function ($a, $b) {
            $scoreA = ($a['ngoai_ke_hoach'] ? 1 : 0) + ($a['da_duyet'] > 0 ? -1 : 0);
            $scoreB = ($b['ngoai_ke_hoach'] ? 1 : 0) + ($b['da_duyet'] > 0 ? -1 : 0);
            return $scoreA <=> $scoreB;
        });

        // Chuyển về mảng index
        $result = array_values($result);

        return $result;
    }

    private function getAndValidateHandoverIn(int $contractId)
    {
        $handoverIn = $this->getHandoverInDraftOrPendingApproval($contractId);
        if (empty($handoverIn)) {
            throw new Exception('Chưa có dữ liệu bàn giao vào');
        }
        return $handoverIn;
    }

    public function getHandoverInsApproved(int $contractId, int $year = null)
    {
        return $this->repository->list([
            'contract_id' => $contractId,
            'nam' => $year,
            'type' => 'in',
            'minute_status' => 'approved',
        ]);
    }

    private function getHandoverInDraftOrPendingApproval(int $contractId, int $year = null)
    {
        $handoverIn = $this->repository->list([
            'contract_id' => $contractId,
            'nam' => $year,
            'type' => 'in',
            'minute_status' => ['draft', 'pending_approval'],
        ]);
        return $handoverIn[0] ?? null;
    }

    private function getAndValidateHandoverOut(int $contractId)
    {
        $handoverOut = $this->getHandoverOut($contractId);
        if (empty($handoverOut)) {
            throw new Exception('Chưa có dữ liệu bàn giao từ kế hoạch');
        }
        return $handoverOut;
    }

    private function getHandoverOut(int $contractId, int $year = null)
    {
        $handoverOut = $this->repository->list([
            'contract_id' => $contractId,
            'type' => 'out',
            'nam' => $year,
        ]);
        return $handoverOut[0] ?? null;
    }

    /**
     * Export excel template với dữ liệu hiện trạng
     * Luôn chỉ xuất dữ liệu kế hoạch + đã duyệt (loại trừ đang chờ duyệt)
     */
    public function createTempExcel(array $request)
    {
        $handoverOut = $this->getAndValidateHandoverOut($request['contract_id']);

        // Lấy dữ liệu hiện trạng (chỉ kế hoạch + đã duyệt, loại trừ đang chờ duyệt)
        $sheetData = $this->getCurrentStatusExcelData($request['contract_id']);

        $dossierService = app(DossierService::class);
        $sheets = $dossierService->baseCreateTempExcel();
        unset($sheets[count($sheets) - 1]);

        $sheets[] = (object) [
            'name' => 'data',
            'header' => [
                [
                    [
                        'name' => 'Loại biên bản',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Tỉnh (kế hoạch)',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Xã (kế hoạch)',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Đơn vị (kế hoạch)',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Tỉnh (thực hiện) - copy bên sheet tỉnh',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Xã (thực hiện) - copy bên sheet tỉnh',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Đơn vị (thực hiện) - copy bên sheet đơn vị',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Số lượng (kế hoạch)',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Số lượng (bàn giao)',
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
            'data' => $sheetData,
            'boldRows' => [1],
            'boldItalicRows' => [],
            'italicRows' => [],
            'centerColumns' => [],
            'centerRows' => [],
            'filterStartRow' => 1,
            'freezePane' => 'freezeTopRow',
        ];

        return asset($dossierService->createExcel(
            'dossier/handover',
            uniqid('dossier_handover_') . '.xlsx',
            $sheets
        ));
    }

    /**
     * Lấy dữ liệu excel hiện trạng (chỉ kế hoạch + đã duyệt)
     * Loại trừ dữ liệu đang chờ duyệt để tránh xung đột
     */
    private function getCurrentStatusExcelData(int $contractId)
    {
        // Lấy dữ liệu hiện trạng (chỉ kế hoạch + đã duyệt, bỏ qua đang chờ duyệt)
        $handoverOut = $this->getHandoverOut($contractId);
        $handoverInsApproved = $this->getHandoverInsApproved($contractId) ?? [];

        // Truyền empty array cho đang chờ duyệt để chỉ lấy kế hoạch + đã duyệt
        $comparison = $this->mergeAndCompareHandovers($handoverOut, [], $handoverInsApproved);

        return array_map(function ($item) {
            return [
                $item['type']['name'] ?? '',
                $item['plan_province']['name'] ?? '',  // Có thể null nếu ngoài kế hoạch
                $item['plan_commune']['name'] ?? '',  // Có thể null nếu ngoài kế hoạch
                $item['plan_unit']['name'] ?? '',  // Có thể null nếu ngoài kế hoạch
                $item['handover_province']['name'] ?? '',  // Địa chỉ từ đã duyệt (nếu có)
                $item['handover_commune']['name'] ?? '',  // Địa chỉ từ đã duyệt (nếu có)
                $item['handover_unit']['name'] ?? '',  // Địa chỉ từ đã duyệt (nếu có)
                $item['ke_hoach'] ?? 0,
                '',  // Cột để nhập số lượng bàn giao mới
                '',  // Ghi chú
            ];
        }, $comparison);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        if (isset($array['minutes']))
            $array['minutes'] = $this->dossierMinuteService->formatRecords($array['minutes']);

        if (isset($array['plan']))
            $array['plan'] = app(DossierPlanService::class)->formatRecord($array['plan']);

        return $array;
    }

    /**
     * Upload excel với logic đơn giản:
     * - Luôn xóa hết dữ liệu đang chờ duyệt
     * - Tạo mới hoàn toàn từ dữ liệu excel
     * - Validate dựa trên dữ liệu hiện trạng (kế hoạch + đã duyệt)
     */
    public function uploadExcel(int $contractId, $file)
    {
        return $this->tryThrow(function () use ($contractId, $file) {
            $data = app(ExcelService::class)->readExcel($file);
            if (!isset($data['data']))
                throw new Exception('File excel bị mất sheet "data" ban đầu - vui lòng giữ nguyên tên sheet!');

            $handoverOut = $this->getAndValidateHandoverOut($contractId);

            // Validate dựa trên dữ liệu hiện trạng (kế hoạch + đã duyệt)
            $result = $this->validateCurrentStatusHandoverData($contractId, $data['data']);

            if (count($result['validationErrors']) > 0)
                throw new Exception(implode("\n", $result['validationErrors']));

            if (count($result['modifiedOriginal']) > 0 || count($result['customRows']) > 0) {
                // Luôn xóa hết draft rồi tạo mới
                $this->repository->deleteHandoverInByContractIdAndMinuteStatus($contractId, 'draft');

                // Tạo bàn giao mới
                $handover = $this->repository->store([
                    'type' => 'in',
                    'dossier_plan_id' => $handoverOut['dossier_plan_id'],
                    'user_id' => auth()->id(),
                    'handover_by' => auth()->id(),
                    'received_by' => app(SystemConfigService::class)->getDossierHandoverReceivedById()['value'],
                    'times' => $this->repository->getMaxTimeHandoverInByContractId($contractId) + 1,
                ]);

                $handoverDetails = [];
                foreach ([$result['modifiedOriginal'], $result['customRows']] as $array) {
                    foreach ($array as $item) {
                        $handoverDetails[] = [
                            'dossier_handover_id' => $handover['id'],
                            'dossier_type_id' => $item['type_id'],
                            'province_code' => $item['province_code'],
                            'commune_code' => $item['commune_code'],
                            'unit_id' => $item['unit_id'],
                            'quantity' => $item['quantity'],
                            'note' => $item['data'][9] ?? null
                        ];
                    }
                }

                $handover->details()->createMany($handoverDetails);
            } else {
                throw new Exception('Không có dữ liệu bàn giao');
            }
        }, true);
    }

    /**
     * Validate dữ liệu excel dựa trên hiện trạng (kế hoạch + đã duyệt)
     * Chỉ xử lý những dòng có dữ liệu bàn giao (cột 8 không rỗng)
     */
    private function validateCurrentStatusHandoverData(int $contractId, array $sheetData)
    {
        // Lấy dữ liệu hiện trạng (kế hoạch + đã duyệt, loại trừ đang chờ duyệt)
        $handoverOut = $this->getHandoverOut($contractId);
        $handoverInsApproved = $this->getHandoverInsApproved($contractId) ?? [];

        $currentStatus = $this->mergeAndCompareHandovers($handoverOut, [], $handoverInsApproved);

        // Tạo map từ dữ liệu hiện trạng
        $statusKeys = [];
        foreach ($currentStatus as $index => $row) {
            $key = implode('|', [
                $row['type']['name'] ?? '',
                $row['plan_province']['name'] ?? '',  // Có thể null nếu ngoài kế hoạch
                $row['plan_commune']['name'] ?? '',  // Có thể null nếu ngoài kế hoạch
                $row['plan_unit']['name'] ?? '',  // Có thể null nếu ngoài kế hoạch
                $row['ke_hoach'] ?? 0
            ]);
            $statusKeys[$key] = $index;
        }

        // Bỏ qua header
        unset($sheetData[0]);

        $modifiedOriginal = $customRows = $validationErrors = [];

        foreach ($sheetData as $index => $row) {
            // Chỉ xử lý những dòng có dữ liệu bàn giao (cột 8)
            if (empty($row[8])) {
                continue;
            }

            if ($this->isCustomRow($row)) {
                // Row tự tạo thêm
                $validation = $this->validateCustomRowFields($row);

                $customRows[] = array_merge([
                    'index' => $index,
                    'data' => $row,
                    'quantity' => $row[8] ?? null,
                    'note' => $row[9] ?? null,
                ], $validation);

                if (!$validation['isValid'])
                    $validationErrors[] = 'Dòng ' . ($index + 1) . ': ' . implode(', ', $validation['errors']);
            } else {
                // Row từ dữ liệu hiện trạng
                $validation = $this->validateOriginRowFields($row);

                if (!$validation['isValid'])
                    $validationErrors[] = 'Dòng ' . ($index + 1) . ': ' . implode(', ', $validation['errors']);

                $key = implode('|', [
                    $row[0] ?? '',
                    $row[1] ?? '',
                    $row[2] ?? '',
                    $row[3] ?? '',
                    $row[7] ?? 0
                ]);

                if (isset($statusKeys[$key])) {
                    $statusRow = $currentStatus[$statusKeys[$key]];

                    $modifiedOriginal[] = array_merge([
                        'index' => $index,
                        'statusIndex' => $statusKeys[$key],
                        'key' => $key,
                        'data' => $row,
                        'quantity' => $row[8] ?? null,
                        'note' => $row[9] ?? null,
                        // Sử dụng thông tin địa chỉ từ hiện trạng, ưu tiên handover nếu có, không thì plan
                        'province_code' => $statusRow['handover_province']['code'] ?? $statusRow['province_code'],
                        'commune_code' => $statusRow['handover_commune']['code'] ?? $statusRow['commune_code'],
                        'unit_id' => $statusRow['handover_unit']['id'] ?? $statusRow['unit_id'],
                        'type_id' => $statusRow['dossier_type_id'],
                    ], $validation);
                } else {
                    $validationErrors[] = 'Dòng ' . ($index + 1) . ': Không tìm thấy trong dữ liệu hiện trạng';
                }
            }
        }

        return [
            'isValid' => empty($validationErrors),
            'modifiedOriginal' => $modifiedOriginal,
            'customRows' => $customRows,
            'validationErrors' => $validationErrors,
        ];
    }

    private function validateCustomRowFields($row)
    {
        $errors = [];
        $type_id = $province_code = $commune_code = $unit_id = null;

        // Cột 0 required
        if (empty($row[0])) {
            $errors[] = 'Loại biên bản là bắt buộc';
        } else {
            $dossierType = $this->dossierTypeService->findByName($row[0]);
            if (!$dossierType) {
                $errors[] = 'Loại biên bản không tồn tại';
            }
            $type_id = $dossierType->id ?? null;
        }

        // Cột 4 required (tỉnh thực hiện)
        if (empty($row[4])) {
            $errors[] = 'Tỉnh thực hiện là bắt buộc';
        }

        // Cột 8 required (số lượng bàn giao)
        if (empty($row[8])) {
            $errors[] = 'Số lượng bàn giao là bắt buộc';
        } else {
            if (!is_numeric($row[8])) {
                $errors[] = 'Số lượng bàn giao phải là số';
            } elseif (!is_int($row[8]) || $row[8] < 0) {
                $errors[] = 'Số lượng bàn giao phải là số nguyên dương';
            }
        }

        // Cột 5 và 6 không thể cùng tồn tại
        $hasCol5 = !empty($row[5]);
        $hasCol6 = !empty($row[6]);

        if ($hasCol5 && $hasCol6) {
            $errors[] = 'Xã thực hiện và Đơn vị thực hiện không thể cùng tồn tại';
        }

        if (!$hasCol5 && !$hasCol6) {
            $errors[] = 'Bắt buộc phải có ít nhất 1 trong 2 trường: Xã thực hiện hoặc Đơn vị thực hiện';
        }

        if (!empty($row[4]) && (!empty($row[5]) || !empty($row[6]))) {
            $province = $this->provinceService->findByName($row[4])->load($this->provinceService->repository->relations)->toArray();
            $province_code = $province['code'] ?? null;

            if (!empty($row[5])) {
                $check = array_filter($province['communes'], function ($commune) use ($row) {
                    return $commune['name'] === $row[5];
                });
                if (count($check) == 0) {
                    $errors[] = 'Xã ' . $row[5] . ' không tồn tại trong tỉnh ' . $row[4];
                }

                $commune_code = reset($check)['code'] ?? null;
            }

            if (!empty($row[6])) {
                $check = array_filter($province['units'], function ($unit) use ($row) {
                    return $unit['name'] === $row[6];
                });
                if (count($check) == 0) {
                    $errors[] = 'Đơn vị ' . $row[6] . ' không tồn tại trong tỉnh ' . $row[4];
                }

                $unit_id = reset($check)['id'] ?? null;
            }
        }

        return [
            'isValid' => empty($errors),
            'errors' => $errors,
            'province_code' => $province_code,
            'commune_code' => $commune_code,
            'unit_id' => $unit_id,
            'type_id' => $type_id,
        ];
    }

    private function validateOriginRowFields($row)
    {
        $errors = [];
        $type_id = $province_code = $commune_code = $unit_id = null;

        // Cột 0 required
        if (empty($row[0])) {
            $errors[] = 'Loại biên bản là bắt buộc';
        } else {
            $dossierType = $this->dossierTypeService->findByName($row[0]);
            if (!$dossierType) {
                $errors[] = 'Loại biên bản không tồn tại';
            }
            $type_id = $dossierType->id ?? null;
        }

        if (!empty($row[8])) {
            if (!is_numeric($row[8])) {
                $errors[] = 'Số lượng bàn giao phải là số';
            } elseif (!is_int($row[8]) || $row[8] < 0) {
                $errors[] = 'Số lượng bàn giao phải là số nguyên dương';
            }
        }

        if (!empty($row[1]) && (!empty($row[2]) || !empty($row[3]))) {
            $province = $this->provinceService->findByName($row[1])->load($this->provinceService->repository->relations)->toArray();
            $province_code = $province['code'] ?? null;

            if (!empty($row[2])) {
                $check = array_filter($province['communes'], function ($commune) use ($row) {
                    return $commune['name'] === $row[2];
                });
                if (count($check) == 0) {
                    $errors[] = 'Xã ' . $row[2] . ' không tồn tại trong tỉnh ' . $row[1];
                }

                $commune_code = reset($check)['code'] ?? null;
            }

            if (!empty($row[3])) {
                $check = array_filter($province['units'], function ($unit) use ($row) {
                    return $unit['name'] === $row[3];
                });
                if (count($check) == 0) {
                    $errors[] = 'Đơn vị ' . $row[3] . ' không tồn tại trong tỉnh ' . $row[1];
                }

                $unit_id = reset($check)['id'] ?? null;
            }
        }

        return [
            'isValid' => empty($errors),
            'errors' => $errors,
            'province_code' => $province_code,
            'commune_code' => $commune_code,
            'unit_id' => $unit_id,
            'type_id' => $type_id,
        ];
    }

    private function isCustomRow($row)
    {
        // Row tự tạo: cột 1,2,3,7 phải null/empty, luôn có cột 4 và có 1 trong cột 5,6
        $hasOriginalData = !empty($row[1]) && !empty($row[2]) && !empty($row[3]) && !empty($row[7]);
        $hasCustomData = !empty($row[4]) && (empty($row[5]) || empty($row[6]));

        return !$hasOriginalData && $hasCustomData;
    }

    public function createMinute(int $contractId)
    {
        return $this->tryThrow(function () use ($contractId) {
            $check = $this->getLastMinuteAndHandoverInByContractId($contractId);
            $handover = $check['handover'];
            $lastMinute = $check['last_minute'];

            $this->dossierMinuteService->validateMinuteStatusWhenCreate($lastMinute);

            $minute = $this->dossierMinuteService->createHandoverMinus($handover->toArray());
            return $minute;
        }, true);
    }

    public function getLastMinuteAndHandoverInByContractId(int $contractId)
    {
        $handover = $this->getAndValidateHandoverIn($contractId);
        // lấy collect model vì hiện tại đang là mảng
        $handover = $this->repository->findById($handover['id']);
        $lastMinute = $this->getLastMinute($handover);

        $handover->update([
            'user_id' => auth()->id(),
            'handover_by' => auth()->id(),
        ]);

        return [
            'handover' => $handover,
            'last_minute' => $lastMinute
        ];
    }

    public function getLastMinute(DossierHandover $handover)
    {
        return $handover->minutes()->latest()->first();
    }

    public function sendApproveRequest(int $contractId)
    {
        return $this->tryThrow(function () use ($contractId) {
            $check = $this->getLastMinuteAndHandoverInByContractId($contractId);
            $handover = $check['handover'];
            $lastMinute = $check['last_minute'];

            $this->dossierMinuteService->validateMinuteStatusWhenSendApproveRequest($lastMinute);

            $lastMinute->update([
                'status' => 'pending_approval',
            ]);

            $this->dossierMinuteService->sendMail(
                'Yêu cầu phê duyệt biên bản giao hồ sơ ngoại nghiệp',
                ['tenhd' => $handover['plan']['contract']['tenhd'], 'type' => 3],
                $lastMinute
            );
        }, true);
    }
}
