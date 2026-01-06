<?php

namespace App\Services;

class VehicleStatisticService extends BaseService
{
    private $monthNames = ['', 'T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];

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
                ->map(fn($v, $k) => [
                    ...$this->vehicleService->getStatus($k),
                    'value' => $v,
                    'detail_key' => 'vehicle_status',
                    'detail_filter' => $k
                ])
                ->values()
                ->toArray();

            // Counter cards cho hoạt động
            $loanStats = $this->vehicleLoanService->statistic($request);
            $loanDetail = $loanStats['returned_not_ready_detail'] ?? [];
            unset($loanStats['returned_not_ready_detail']);

            // Thêm detail_key cho loan stats
            $activityCounter = collect($loanStats)->map(function ($item) {
                $item['detail_key'] = 'vehicle_loan';
                $item['detail_filter'] = $item['original'];
                return $item;
            })->values()->toArray();

            // Cảnh báo sắp hết hạn
            $expiryWarnings = $this->vehicleService->getExpiryWarnings();
            $warningCounter = $this->getWarningCounter($expiryWarnings);

            // Charts data
            $loanByMonthChart = $this->buildMonthlyChart($request, 'loan');
            $costByMonthChart = $this->buildMonthlyChart($request, 'cost');
            $kmByMonthChart = $this->buildMonthlyChart($request, 'km');
            $topVehiclesChart = $this->getTopVehiclesChart($request);

            return [
                'counter_current_status' => $currentStatusCounter,
                'counter_activity' => $activityCounter,
                'counter_warnings' => $warningCounter,
                'loan_returned_not_ready_detail' => $loanDetail,
                'expiry_warnings' => $expiryWarnings,
                'charts' => [
                    'loan_by_month' => $loanByMonthChart,
                    'cost_by_month' => $costByMonthChart,
                    'km_by_month' => $kmByMonthChart,
                    'top_vehicles' => $topVehiclesChart,
                ],
            ];
        });
    }

    private function getWarningCounter($warnings)
    {
        $config = [
            'inspection' => [
                'converted' => 'Sắp hết hạn đăng kiểm',
                'color' => 'danger',
                'icon' => 'ti ti-calendar-exclamation',
            ],
            'liability_insurance' => [
                'converted' => 'Sắp hết hạn BH trách nhiệm',
                'color' => 'orange',
                'icon' => 'ti ti-shield-exclamation',
            ],
            'body_insurance' => [
                'converted' => 'Sắp hết hạn BH thân vỏ',
                'color' => 'info',
                'icon' => 'ti ti-shield-check',
            ],
        ];

        return collect($config)->map(function($item, $key) use ($warnings) {
            return [
                'original' => "{$key}_warning",
                'converted' => $item['converted'],
                'color' => $item['color'],
                'icon' => $item['icon'],
                'value' => $warnings[$key]->count(),
                'detail_key' => 'warning',
                'detail_filter' => $key,
            ];
        })->values()->toArray();
    }

    /**
     * Build monthly chart data with configuration
     *
     * @param array $request
     * @param string $type - 'loan', 'cost', 'km'
     * @return array
     */
    private function buildMonthlyChart(array $request, string $type)
    {
        $data = $this->vehicleLoanService->statisticByMonth($request);

        // Configuration cho từng loại chart
        $config = [
            'loan' => [
                'series' => [
                    ['name' => 'Số lượt mượn', 'field' => 'total']
                ]
            ],
            'cost' => [
                'series' => [
                    ['name' => 'Chi phí xăng', 'field' => 'total_fuel_cost'],
                    ['name' => 'Chi phí bảo dưỡng', 'field' => 'total_maintenance_cost']
                ]
            ],
            'km' => [
                'series' => [
                    ['name' => 'Tổng km', 'field' => 'total_km']
                ]
            ],
        ];

        $chartConfig = $config[$type] ?? $config['loan'];

        // Nếu filter theo tháng cụ thể
        if (isset($request['month'])) {
            $monthData = $data->firstWhere('month', $request['month']);

            return [
                'categories' => [$this->monthNames[$request['month']]],
                'series' => collect($chartConfig['series'])->map(function($series) use ($monthData) {
                    return [
                        'name' => $series['name'],
                        'data' => [$monthData[$series['field']] ?? 0]
                    ];
                })->toArray()
            ];
        }

        // Hiển thị tất cả 12 tháng
        $result = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $data->firstWhere('month', $month);
            $monthResult = ['category' => $this->monthNames[$month]];

            foreach ($chartConfig['series'] as $series) {
                $monthResult[$series['field']] = $monthData[$series['field']] ?? 0;
            }

            $result[] = $monthResult;
        }

        return [
            'categories' => array_column($result, 'category'),
            'series' => collect($chartConfig['series'])->map(function($series) use ($result) {
                return [
                    'name' => $series['name'],
                    'data' => array_column($result, $series['field'])
                ];
            })->toArray()
        ];
    }

    private function getTopVehiclesChart(array $request)
    {
        $data = $this->vehicleLoanService->topVehicles($request);

        return [
            'categories' => $data->pluck('vehicle_name')->toArray(),
            'series' => [
                [
                    'name' => 'Số lượt mượn',
                    'data' => $data->pluck('total')->toArray(),
                ]
            ],
        ];
    }
}
