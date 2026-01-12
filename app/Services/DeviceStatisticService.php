<?php

namespace App\Services;

class DeviceStatisticService extends BaseService
{
    private $monthNames = ['', 'T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];

    public function __construct(
        private DeviceService $deviceService,
        private DeviceLoanService $deviceLoanService,
        private DeviceFixService $deviceFixService,
    ) {}

    public function data(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            // Lấy statistic 1 lần duy nhất
            $deviceStatistic = $this->deviceService->statistic();

            // Counter cards cho trạng thái hiện tại
            $currentStatusCounter = collect($deviceStatistic)
                ->map(fn($v, $k) => [...$this->deviceService->getStatus($k), 'value' => $v])
                ->values()
                ->toArray();

            // Counter cards cho hoạt động (fix & loan)
            $fixStats = $this->deviceFixService->statistic($request);
            $loanStats = $this->deviceLoanService->statistic($request);

            $loanDetail = $loanStats['returned_not_normal_detail'] ?? [];
            unset($loanStats['returned_not_normal_detail']);

            $activityCounter = array_merge(
                array_values($fixStats),
                array_values($loanStats)
            );

            // Charts data
            $statusChart = $this->getStatusChartData($deviceStatistic);
            $loanByMonthChart = $this->getLoanByMonthChart($request);
            $fixCostByMonthChart = $this->getFixCostByMonthChart($request);
            $statusByTypeChart = $this->getStatusByTypeChart();

            return [
                'counter_current_status' => $currentStatusCounter,
                'counter_activity' => $activityCounter,
                'loan_returned_not_normal_detail' => $loanDetail,
                'charts' => [
                    'status_pie' => $statusChart,
                    'loan_by_month' => $loanByMonthChart,
                    'fix_cost_by_month' => $fixCostByMonthChart,
                    'status_by_type' => $statusByTypeChart,
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
                $status = $this->deviceService->getStatus($key);
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
        $data = $this->deviceLoanService->statisticByMonth($request);

        // Nếu filter theo tháng cụ thể, chỉ trả về tháng đó
        if (isset($request['month'])) {
            $monthData = $data->firstWhere('month', $request['month']);
            return [
                'categories' => [$this->monthNames[$request['month']]],
                'series' => [
                    [
                        'name' => 'Số lượt mượn',
                        'data' => [$monthData['total'] ?? 0],
                    ]
                ],
            ];
        }

        // Nếu không filter theo tháng, hiển thị tất cả 12 tháng
        $result = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $data->firstWhere('month', $month);
            $result[] = [
                'category' => $this->monthNames[$month],
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

    private function getFixCostByMonthChart(array $request)
    {
        $data = $this->deviceFixService->statisticCostByMonth($request);

        // Nếu filter theo tháng cụ thể, chỉ trả về tháng đó
        if (isset($request['month'])) {
            $monthData = $data->firstWhere('month', $request['month']);
            return [
                'categories' => [$this->monthNames[$request['month']]],
                'series' => [
                    [
                        'name' => 'Số lượt sửa',
                        'data' => [$monthData['total'] ?? 0],
                    ],
                    [
                        'name' => 'Chi phí (VNĐ)',
                        'data' => [$monthData['total_cost'] ?? 0],
                    ]
                ],
            ];
        }

        // Nếu không filter theo tháng, hiển thị tất cả 12 tháng
        $result = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $data->firstWhere('month', $month);
            $result[] = [
                'category' => $this->monthNames[$month],
                'total' => $monthData['total'] ?? 0,
                'total_cost' => $monthData['total_cost'] ?? 0,
            ];
        }

        return [
            'categories' => array_column($result, 'category'),
            'series' => [
                [
                    'name' => 'Số lượt sửa',
                    'data' => array_column($result, 'total'),
                ],
                [
                    'name' => 'Chi phí (VNĐ)',
                    'data' => array_column($result, 'total_cost'),
                ]
            ],
        ];
    }

    private function getStatusByTypeChart()
    {
        $data = $this->deviceService->statisticStatusByType();

        $categories = [];
        $series = [
            ['name' => 'Bình thường', 'data' => []],
            ['name' => 'Đang mượn', 'data' => []],
            ['name' => 'Sửa chữa', 'data' => []],
            ['name' => 'Hỏng', 'data' => []],
            ['name' => 'Lỗi', 'data' => []],
            ['name' => 'Thất Lạc', 'data' => []],
            ['name' => 'Lưu kho', 'data' => []],
        ];

        foreach ($data as $item) {
            $categories[] = $item->deviceType->name ?? 'N/A';
            $series[0]['data'][] = (int)$item->normal;
            $series[1]['data'][] = (int)$item->loaned;
            $series[2]['data'][] = (int)$item->under_repair;
            $series[3]['data'][] = (int)$item->broken;
            $series[4]['data'][] = (int)$item->faulty;
            $series[5]['data'][] = (int)$item->lost;
            $series[6]['data'][] = (int)$item->stored;
        }

        return [
            'categories' => $categories,
            'series' => $series,
        ];
    }
}
