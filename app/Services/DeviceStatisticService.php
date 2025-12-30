<?php

namespace App\Services;

class DeviceStatisticService extends BaseService
{
    public function __construct(
        private DeviceService $deviceService,
        private DeviceLoanService $deviceLoanService,
        private DeviceFixService $deviceFixService,
    ) {}

    public function data(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            // Lấy counter từ device status
            $deviceCounter = collect($this->deviceService->statistic())
                ->map(fn($v, $k) => [...$this->deviceService->getStatus($k), 'value' => $v])
                ->values()
                ->toArray();

            // Lấy counter từ device fix
            $fixStats = $this->deviceFixService->statistic($request);

            // Lấy stats từ device loan
            $loanStats = $this->deviceLoanService->statistic($request);

            // Tách detail ra khỏi loanStats
            $loanDetail = $loanStats['returned_not_normal_detail'] ?? [];
            unset($loanStats['returned_not_normal_detail']);

            // Merge tất cả counter lại
            $counter = array_merge(
                $deviceCounter,
                array_values($fixStats),
                array_values($loanStats)
            );

            return [
                'counter' => $counter,
                'loan_returned_not_normal_detail' => $loanDetail,
            ];
        });
    }
}
