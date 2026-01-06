<?php

namespace App\Services;

class VehicleStatisticService extends BaseService
{
    public function __construct(
        private VehicleService $vehicleService,
        private VehicleLoanService $vehicleLoanService
    ) {}

    public function data(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            // Lấy statistic 1 lần duy nhất
            $vehicleStatistic = $this->vehicleService->statistic();

            // Counter cards cho trạng thái hiện tại
            $currentStatusCounter = collect($vehicleStatistic)
                ->map(fn($v, $k) => [...$this->vehicleService->getStatus($k), 'value' => $v])
                ->values()
                ->toArray();

            // Counter cards cho hoạt động
            $loanStats = $this->vehicleLoanService->statistic($request);

            $loanDetail = $loanStats['returned_not_ready_detail'] ?? [];
            unset($loanStats['returned_not_ready_detail']);

            // Charts data
            $statusChart = $this->getStatusChartData($vehicleStatistic);

            return [
                'counter_current_status' => $currentStatusCounter,
                'counter_activity' => array_values($loanStats),
                'loan_returned_not_ready_detail' => $loanDetail,
                'charts' => [
                    'status_pie' => $statusChart,
                ],
            ];
        });
    }

    private function getStatusChartData($statistic)
    {
        $labels = [];
        $series = [];

        foreach ($statistic as $key => $value) {
            if ($value > 0) {
                $status = $this->vehicleService->getStatus($key);
                $labels[] = $status['converted'];
                $series[] = (int)$value;
            }
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }
}
