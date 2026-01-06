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

            // Cảnh báo sắp hết hạn
            $expiryWarnings = $this->vehicleService->getExpiryWarnings();
            $warningCounter = $this->getWarningCounter($expiryWarnings);

            // Charts data
            $statusChart = $this->getStatusChartData($vehicleStatistic);
            $loanByMonthChart = $this->getLoanByMonthChart($request);
            $costByMonthChart = $this->getCostByMonthChart($request);
            $kmByMonthChart = $this->getKmByMonthChart($request);
            $topVehiclesChart = $this->getTopVehiclesChart($request);

            return [
                'counter_current_status' => $currentStatusCounter,
                'counter_activity' => array_values($loanStats),
                'counter_warnings' => $warningCounter,
                'loan_returned_not_ready_detail' => $loanDetail,
                'expiry_warnings' => $expiryWarnings,
                'charts' => [
                    'status_pie' => $statusChart,
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
        return [
            [
                'original' => 'inspection_warning',
                'converted' => 'Sắp hết hạn đăng kiểm',
                'color' => 'danger',
                'icon' => 'ti ti-calendar-exclamation',
                'value' => $warnings['inspection']->count(),
            ],
            [
                'original' => 'insurance_warning',
                'converted' => 'Sắp hết hạn bảo hiểm',
                'color' => 'orange',
                'icon' => 'ti ti-shield-exclamation',
                'value' => $warnings['liability_insurance']->count(),
            ],
        ];
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

    private function getLoanByMonthChart(array $request)
    {
        $data = $this->vehicleLoanService->statisticByMonth($request);
        $monthNames = ['', 'T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];

        if (isset($request['month'])) {
            $monthData = $data->firstWhere('month', $request['month']);
            return [
                'categories' => [$monthNames[$request['month']]],
                'series' => [
                    [
                        'name' => 'Số lượt mượn',
                        'data' => [$monthData['total'] ?? 0],
                    ]
                ],
            ];
        }

        $result = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $data->firstWhere('month', $month);
            $result[] = [
                'category' => $monthNames[$month],
                'total' => $monthData['total'] ?? 0,
            ];
        }

        return [
            'categories' => array_column($result, 'category'),
            'series' => [
                [
                    'name' => 'Số lượt mượn',
                    'data' => array_column($result, 'total'),
                ]
            ],
        ];
    }

    private function getCostByMonthChart(array $request)
    {
        $data = $this->vehicleLoanService->statisticByMonth($request);
        $monthNames = ['', 'T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];

        if (isset($request['month'])) {
            $monthData = $data->firstWhere('month', $request['month']);
            return [
                'categories' => [$monthNames[$request['month']]],
                'series' => [
                    [
                        'name' => 'Chi phí xăng',
                        'data' => [$monthData['total_fuel_cost'] ?? 0],
                    ],
                    [
                        'name' => 'Chi phí bảo dưỡng',
                        'data' => [$monthData['total_maintenance_cost'] ?? 0],
                    ]
                ],
            ];
        }

        $result = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $data->firstWhere('month', $month);
            $result[] = [
                'category' => $monthNames[$month],
                'fuel' => $monthData['total_fuel_cost'] ?? 0,
                'maintenance' => $monthData['total_maintenance_cost'] ?? 0,
            ];
        }

        return [
            'categories' => array_column($result, 'category'),
            'series' => [
                [
                    'name' => 'Chi phí xăng',
                    'data' => array_column($result, 'fuel'),
                ],
                [
                    'name' => 'Chi phí bảo dưỡng',
                    'data' => array_column($result, 'maintenance'),
                ]
            ],
        ];
    }

    private function getKmByMonthChart(array $request)
    {
        $data = $this->vehicleLoanService->statisticByMonth($request);
        $monthNames = ['', 'T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];

        if (isset($request['month'])) {
            $monthData = $data->firstWhere('month', $request['month']);
            return [
                'categories' => [$monthNames[$request['month']]],
                'series' => [
                    [
                        'name' => 'Tổng km',
                        'data' => [$monthData['total_km'] ?? 0],
                    ]
                ],
            ];
        }

        $result = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $data->firstWhere('month', $month);
            $result[] = [
                'category' => $monthNames[$month],
                'km' => $monthData['total_km'] ?? 0,
            ];
        }

        return [
            'categories' => array_column($result, 'category'),
            'series' => [
                [
                    'name' => 'Tổng km',
                    'data' => array_column($result, 'km'),
                ]
            ],
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
