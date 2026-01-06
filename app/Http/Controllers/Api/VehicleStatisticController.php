<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleStatistic\DataRequest;
use App\Http\Requests\VehicleStatistic\DetailRequest;
use App\Services\VehicleLoanService;
use App\Services\VehicleService;
use App\Services\VehicleStatisticService;

class VehicleStatisticController extends Controller
{
    public function __construct(
        private VehicleStatisticService $vehicleStatisticService,
        private VehicleService $vehicleService,
        private VehicleLoanService $vehicleLoanService
    ) {}

    public function data(DataRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->vehicleStatisticService->data($request->validated()),
        ]));
    }

    public function detail(DetailRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $params = $request->validated();
            $filter = $params['filter'] ?? '';

            $data = match ($params['type']) {
                'vehicle_status' => $this->vehicleService->list([
                    'status' => $filter,
                    'load_relations' => true,
                ]),
                'vehicle_loan' => $this->getVehicleLoanDetail($filter, $params),
                'warning' => $this->getWarningDetail($filter),
                default => []
            };

            return response()->json(['data' => $data]);
        });
    }

    private function getVehicleLoanDetail(string $filter, array $params)
    {
        // Validate filter value cho vehicle_loan
        $validFilters = ['total_loans', 'not_returned', 'returned_not_ready_count', 'total_fuel_cost', 'total_maintenance_cost'];

        if (!in_array($filter, $validFilters)) {
            throw new \InvalidArgumentException('Bộ lọc không hợp lệ');
        }

        $conditions = match ($filter) {
            'total_loans' => [
                'statuses' => ['approved', 'returned']
            ],
            'not_returned' => [
                'status' => 'approved',
                'returned_at' => 'null'
            ],
            'returned_not_ready_count' => [
                'status' => 'returned',
                'vehicle_status_return_not' => 'ready'
            ],
            'total_fuel_cost' => [
                'status' => 'returned',
                'has_fuel_cost' => true
            ],
            'total_maintenance_cost' => [
                'status' => 'returned',
                'has_maintenance_cost' => true
            ],
        };

        // Thêm filter theo thời gian nếu có
        if (isset($params['year'])) {
            $conditions['year'] = $params['year'];
        }
        if (isset($params['month'])) {
            $conditions['month'] = $params['month'];
        }

        return $this->vehicleLoanService->list($conditions);
    }

    private function getWarningDetail(string $filter)
    {
        $warnings = $this->vehicleService->getExpiryWarnings();

        return match ($filter) {
            'inspection' => $warnings['inspection'],
            'liability_insurance' => $warnings['liability_insurance'],
            'body_insurance' => $warnings['body_insurance'],
            default => throw new \InvalidArgumentException('Loại cảnh báo không hợp lệ'),
        };
    }
}
