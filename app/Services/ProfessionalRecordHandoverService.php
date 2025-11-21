<?php
namespace App\Services;

use App\Models\ProfessionalRecordHandover;
use App\Repositories\ProfessionalRecordHandoverRepository;
use Exception;

class ProfessionalRecordHandoverService extends BaseService
{
    private $provinceService;
    private $professionalRecordTypeService;
    private $professionalRecordMinuteService;

    public function __construct()
    {
        $this->repository = app(ProfessionalRecordHandoverRepository::class);
        $this->provinceService = app(ProvinceService::class);
        $this->professionalRecordTypeService = app(ProfessionalRecordTypeService::class);
        $this->professionalRecordMinuteService = app(ProfessionalRecordMinuteService::class);
    }

    public function baseIndexData()
    {
        return $this->tryThrow(function () {
            $res = app(ProfessionalRecordService::class)->baseIndexData();
            $res['showCreateMinuteBtn'] = true;
            $res['pageTitle'] = 'Bàn giao';
            return $res;
        });
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
            $handoverInDraftOrPendingApproval = optional($this->getHandoverInDraftOrPendingApproval($contractId, $year))->toArray() ?? [];
            $handoverInsApproved = optional($this->getHandoverInsApproved($contractId, $year))->toArray() ?? [];

            $comparison = $this->mergeAndCompareHandovers($handoverOut, $handoverInDraftOrPendingApproval, $handoverInsApproved);

            $handoverInDraftOrPendingApproval = $this->formatRecord($handoverInDraftOrPendingApproval);

            return [
                'comparison' => $comparison,
                'flag' => [
                    'times' => $handoverInDraftOrPendingApproval['times'] ?? collect($handoverInsApproved)->first()['times'] ?? 0,
                    'has_data' => isset($handoverInDraftOrPendingApproval['details']) && count($handoverInDraftOrPendingApproval['details']) > 0,
                    'minutes' => $handoverInDraftOrPendingApproval['minutes'] ?? null,
                    'user' => $handoverInDraftOrPendingApproval['user'] ?? null,
                ],
            ];
        });
    }

    private function mergeAndCompareHandovers($plan, $waitingApprove, $approved)
    {
        $createKey = function ($item) {
            return implode('_', [
                $item['professional_record_type_id'] ?? '',
                $item['province_code'] ?? '',
                $item['commune_code'] ?? '',
                $item['unit_id'] ?? 'null'
            ]);
        };

        // Lấy mảng details từ các tham số
        $keHoachDetails = isset($plan['details']) ? $plan['details'] : [];
        $dangChoDuyetDetails = isset($waitingApprove['details']) ? $waitingApprove['details'] : [];

        // Xử lý mảng đã duyệt (có thể có nhiều record)
        $daDuyetDetails = [];
        $dangChoDuyetId = $waitingApprove['id'] ?? null;

        if (is_array($approved)) {
            foreach ($approved as $item) {
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
                'professional_record_type_id' => $item['professional_record_type_id'],
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
                'plan' => $item['quantity'],
                'waiting_approve' => 0,
                'approved' => 0,
                'out_plan' => false,
                'note' => []
            ];
        }

        // Xử lý đang chờ duyệt
        foreach ($dangChoDuyetDetails as $item) {
            $key = $createKey($item);

            if (!isset($result[$key])) {
                // Nếu không có trong kế hoạch -> tạo mới và đánh dấu ngoài kế hoạch
                $result[$key] = [
                    'professional_record_type_id' => $item['professional_record_type_id'],
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
                    'plan' => 0,
                    'waiting_approve' => 0,
                    'approved' => 0,
                    'out_plan' => true,
                    'note' => []
                ];
            }

            $result[$key]['waiting_approve'] += $item['quantity'];

            // Thêm ghi chú nếu có
            if (!empty($item['note'])) {
                $result[$key]['note'][] = $item['note'];
            }
        }

        // Xử lý đã duyệt
        foreach ($daDuyetDetails as $item) {
            $key = $createKey($item);

            if (!isset($result[$key])) {
                // Nếu không có trong kế hoạch -> tạo mới và đánh dấu ngoài kế hoạch
                $result[$key] = [
                    'professional_record_type_id' => $item['professional_record_type_id'],
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
                    'plan' => 0,
                    'waiting_approve' => 0,
                    'approved' => 0,
                    'out_plan' => true,
                    'note' => []
                ];
            }

            $result[$key]['approved'] += $item['quantity'];

            // Thêm ghi chú nếu có
            if (!empty($item['note'])) {
                $result[$key]['note'][] = $item['note'];
            }
        }

        // Tính toán thêm các chỉ số và xử lý ghi chú
        foreach ($result as &$row) {
            $row['total_approve'] = $row['approved'] + $row['waiting_approve'];

            // Tính còn lại (luôn >= 0)
            $row['total_left'] = max(0, $row['plan'] - $row['approved']);

            // Gộp các ghi chú thành chuỗi (loại bỏ trùng lặp)
            if (!empty($row['note']))
                $row['note'] = !empty($row['note'])
                    // ? implode('; ', array_unique($row['note'])) // nối chuỗi các note
                    ? reset($row['note'])  // lấy note cuối cùng
                    : '';
        }
        unset($row);

        // Sắp xếp: trong kế hoạch trước, ngoài kế hoạch sau
        usort($result, function ($a, $b) {
            $scoreA = ($a['out_plan'] ? 1 : 0) + ($a['approved'] > 0 ? -1 : 0);
            $scoreB = ($b['out_plan'] ? 1 : 0) + ($b['approved'] > 0 ? -1 : 0);
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
            'year' => $year,
            'type' => 'in',
            'minute_status' => 'approved',
        ]);
    }

    private function getHandoverInDraftOrPendingApproval(int $contractId, int $year = null)
    {
        $handoverIn = $this->repository->list([
            'contract_id' => $contractId,
            'year' => $year,
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
            'year' => $year,
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

        $professionalRecordService = app(ProfessionalRecordService::class);
        $sheets = $professionalRecordService->baseCreateTempExcel();
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

        return asset($professionalRecordService->createExcel(
            'uploads/professional-record/handover',
            'professional_record_handover_' . date('d-m-Y_H-i-s') . '.xlsx',
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
                $item['plan'] ?? 0,
                '',  // Cột để nhập số lượng bàn giao mới
                '',  // Ghi chú
            ];
        }, $comparison);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        if (isset($array['minutes']))
            $array['minutes'] = $this->professionalRecordMinuteService->formatRecords($array['minutes']);

        if (isset($array['plan']))
            $array['plan'] = app(ProfessionalRecordPlanService::class)->formatRecord($array['plan']);

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
                    'professional_record_plan_id' => $handoverOut['professional_record_plan_id'],
                    'user_id' => auth()->id(),
                    'handover_by' => auth()->id(),
                    'received_by' => app(SystemConfigService::class)->getProfessionalRecordHandoverReceivedById()['value'],
                    'times' => $this->repository->getMaxTimeHandoverInByContractId($contractId) + 1,
                ]);

                $handoverDetails = [];
                foreach ([$result['modifiedOriginal'], $result['customRows']] as $array) {
                    foreach ($array as $item) {
                        $handoverDetails[] = [
                            'professional_record_handover_id' => $handover['id'],
                            'professional_record_type_id' => $item['type_id'],
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
                $row['plan'] ?? 0
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
                        'type_id' => $statusRow['professional_record_type_id'],
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
            $professionalRecordType = $this->professionalRecordTypeService->findByName($row[0]);
            if (!$professionalRecordType) {
                $errors[] = 'Loại biên bản không tồn tại';
            }
            $type_id = $professionalRecordType->id ?? null;
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
            } elseif (!ctype_digit((string) $row[8])) {
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
            $professionalRecordType = $this->professionalRecordTypeService->findByName($row[0]);
            if (!$professionalRecordType) {
                $errors[] = 'Loại biên bản không tồn tại';
            }
            $type_id = $professionalRecordType->id ?? null;
        }

        if (!empty($row[8])) {
            if (!is_numeric($row[8])) {
                $errors[] = 'Số lượng bàn giao phải là số';
            } elseif (!ctype_digit((string) $row[8])) {
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

            $this->professionalRecordMinuteService->validateMinuteStatusWhenCreate($lastMinute);

            $minute = $this->professionalRecordMinuteService->createHandoverMinus($handover->toArray());
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

    public function getLastMinute(ProfessionalRecordHandover $handover)
    {
        return $handover->minutes()->latest()->first();
    }

    public function sendApproveRequest(int $contractId)
    {
        return $this->tryThrow(function () use ($contractId) {
            $check = $this->getLastMinuteAndHandoverInByContractId($contractId);
            $handover = $check['handover'];
            $lastMinute = $check['last_minute'];

            $this->professionalRecordMinuteService->validateMinuteStatusWhenSendApproveRequest($lastMinute);

            $lastMinute->update([
                'status' => 'pending_approval',
            ]);

            $this->professionalRecordMinuteService->sendMail(
                'Yêu cầu phê duyệt biên bản giao hồ sơ chuyên môn',
                ['name' => $handover['plan']['contract']['name'], 'type' => 3],
                $lastMinute
            );
        }, true);
    }
}
