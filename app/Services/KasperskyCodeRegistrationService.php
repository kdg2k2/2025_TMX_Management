<?php

namespace App\Services;

use App\Repositories\KasperskyCodeRegistrationRepository;
use Exception;

class KasperskyCodeRegistrationService extends BaseService
{
    public function __construct(
        private UserService $userService,
        private DeviceLoanService $deviceLoanService,
        private KasperskyCodeService $kasperskyCodeService,
        private ExcelService $excelService,
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(KasperskyCodeRegistrationRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['type']))
            $array['type'] = $this->repository->getType($array['type']);
        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);
        if (isset($array['codes']))
            $array['codes'] = $this->kasperskyCodeService->formatRecords($array['codes']);
        if (isset($array['approved_at']))
            $array['approved_at'] = $this->formatDateTimeForPreview($array['approved_at']);
        return $array;
    }

    public function getBaseDataForLCView(bool $isListView = false)
    {
        return [
            'devices' => $this->deviceLoanService->list([
                'created_by' => $isListView ? $this->getUserId() : null,
                'status' => $isListView ? 'approved' : null,
                'search' => 'may tinh'
            ]),
            'types' => $this->repository->getType(),
            'statuses' => $this->repository->getStatus(),
            'codes' => $this->kasperskyCodeService->list([
                'is_expired' => false,
                'is_quantity_exceeded' => false,
            ]),
        ];
    }

    protected function beforeStore(array $request)
    {
        if (isset($request['device_id']))
            if ($this->repository->checkUniqueDeviceRegistration($request['device_id']))
                throw new Exception('Thiết bị này đã được yêu cầu đăng ký mã rồi');
        return $request;
    }

    protected function afterStore($data, array $request)
    {
        $this->sendMail($data['id'], 'Yêu cầu');
    }

    public function approve(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $codes = $request['codes'] ?? [];
            unset($request['codes']);

            $data = $this->repository->findById($request['id']);
            $assignedCodes = $this->assignSlotsToRegistration($data, $codes);
            $data->update($request);
            $this->sendMail($data['id'], 'Phê duyệt', [
                'assignedCodes' => $assignedCodes,
            ]);

            return $data;
        }, true);
    }

    private function assignSlotsToRegistration($registration, array $codeIds)
    {
        // Tính số lượt cần cấp
        $requiredSlots = $registration['type'] === 'both' ? 2 : 1;

        $codes = $this->kasperskyCodeService->findByKeys($codeIds, 'id');

        // Validate tổng lượt còn lại
        $totalAvailable = $codes->sum('available_quantity');

        if ($totalAvailable < $requiredSlots)
            throw new Exception("Không đủ lượt sử dụng! Cần {$requiredSlots} lượt, còn {$totalAvailable} lượt.");

        $slotsToAssign = $requiredSlots;
        $assignedCodes = [];

        foreach ($codes as $code) {
            if ($slotsToAssign <= 0)
                break;

            // Số lượt sẽ lấy từ mã này
            $slotsFromThisCode = min($slotsToAssign, $code['available_quantity']);

            $params = [
                'used_quantity' => $code['used_quantity'] + $slotsFromThisCode,
                'available_quantity' => $code['available_quantity'] - $slotsFromThisCode,
            ];

            // Check xem sau khi cấp có hết lượt không
            if ($params['available_quantity'] <= 0)
                $params['is_quantity_exceeded'] = true;

            // Set started_at và expired_at lần đầu sử dụng
            if (empty($code['started_at'])) {
                $params['started_at'] = now()->format('Y-m-d');
                $params['expired_at'] = now()->addDays($code['valid_days'])->format('Y-m-d');
            }

            $code->update($params);

            // Track mã đã cấp
            $assignedCodes[] = $code;

            // Giảm số lượt còn cần cấp
            $slotsToAssign -= $slotsFromThisCode;
        }

        // Sync vào bảng pivot
        $registration->codes()->sync(collect($assignedCodes)->pluck('id')->toArray());
        return collect($assignedCodes)->pluck('code')->toArray();
    }

    public function reject(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Từ chối');
        }, true);
    }

    private function sendMail(int $id, string $subject, array $dataMail = [])
    {
        $data = $this->findById($id, true, true);
        $emails = $this->getEmails($data);
        dispatch(new \App\Jobs\SendMailJob('emails.kaspersky-code', $subject . ' đăng ký mã kaspersky', $emails, [
            'data' => $data,
            ...$dataMail
        ]));
    }

    private function getEmails(array $data)
    {
        return $this->userService->getEmails([
            $data['created_by']['id'],
            $data['approved_by']['id'] ?? null,
            app(TaskScheduleService::class)->getUserIdByScheduleKey('KASPERSKY_CODE')
        ]);
    }

    public function registrationApprovedIsntExpired()
    {
        return $this->tryThrow(fn() => $this->repository->registrationApprovedIsntExpired());
    }

    public function statistic(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $dataExcel = $this->getStatisticDataExcel($request);
            $headerExcel = $this->getStatisticHeaderExcel();
            $baseFileName = 'kaspersky-code-statistic';
            $folder = "uploads/render/$baseFileName";
            $this->handlerUploadFileService->cleanupOldOverlapFiles($folder);
            return $this->getAssetUrl($this->excelService->createExcel([
                (object) [
                    'name' => $baseFileName,
                    'header' => $headerExcel,
                    'data' => $dataExcel,
                    'boldRows' => [1],
                    'boldItalicRows' => [],
                    'italicRows' => [],
                    'centerColumns' => [],
                    'centerRows' => [],
                    'filterStartRow' => 1,
                    'freezePane' => 'freezeTopRow',
                ],
            ], $folder, $baseFileName . '_' . date('d-m-Y-H-i-s') . '.xlsx'));
        });
    }

    private function getStatisticHeaderExcel()
    {
        return [
            [
                ...collect([
                    'Người đăng ký',
                    'Lý do',
                    'Kiểu đăng ký',
                    'Thông tin thiết bị (công ty)',
                    'Người duyệt',
                    'Thời gian duyệt',
                    'Ghi chú duyệt',
                    'Mã',
                    'Số ngày còn hiệu lực',
                ])->map(fn($item) => [
                    'name' => $item,
                    'rowspan' => 1,
                    'colspan' => 1,
                ])->toArray()
            ]
        ];
    }

    private function getStatisticDataExcel(array $request)
    {
        $data = $this->formatRecords($this->repository->statistic($request)->toArray());
        $dataExcel = [];

        foreach ($data as $registration) {
            if (empty($registration['codes']))
                continue;

            foreach ($registration['codes'] as $code) {
                $dataExcel[] = [
                    $registration['created_by']['name'] ?? 'N/A',
                    $registration['reason'] ?? '',
                    $registration['type']['converted'] ?? 'N/A',
                    !empty($registration['device']) ? implode(' - ', array_filter([
                        $registration['device']['device_type']['name'] ?? '',
                        $registration['device']['name'] ?? '',
                        $registration['device']['code'] ?? '',
                    ])) : 'N/A',
                    $registration['approved_by']['name'] ?? 'N/A',
                    $registration['approved_at'] ?? '',
                    $registration['approval_note'] ?? '',
                    $code['code'] ?? 'N/A',
                    $code['remaining_days'] ?? 'N/A',
                ];
            }
        }

        return $dataExcel;
    }
}
