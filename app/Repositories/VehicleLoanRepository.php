<?php

namespace App\Repositories;

use App\Models\VehicleLoan;
use Carbon\Carbon;

class VehicleLoanRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new VehicleLoan();
        $this->relations = [
            'vehicle',
            'createdBy:id,name',
            'approvedBy:id,name',
            'fuelCostPaidBy:id,name',
            'maintenanceCostPaidBy:id,name',
        ];
    }

    public function getStatusReturn($key = null)
    {
        return $this->model->getStatusReturn($key);
    }

    public function getStatus($key = null)
    {
        return $this->model->getStatus($key);
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['status']))
            $query->where('status', $request['status']);
        if (isset($request['statuses']))
            $query->whereIn('status', $request['statuses']);
        if (isset($request['vehicle_status_return']))
            $query->where('vehicle_status_return', $request['vehicle_status_return']);
        if (isset($request['vehicle_status_return_not']))
            $query->where('vehicle_status_return', '!=', $request['vehicle_status_return_not']);
        if (isset($request['created_by']))
            $query->where('created_by', $request['created_by']);
        if (isset($request['vehicle_id']))
            $query->where('vehicle_id', $request['vehicle_id']);
        if (isset($request['returned_at']) && $request['returned_at'] === 'null')
            $query->whereNull('returned_at');
        if (isset($request['year']))
            $query->whereYear('created_at', $request['year']);
        if (isset($request['month']))
            $query->whereMonth('created_at', $request['month']);

        // Filter có chi phí xăng
        if (isset($request['has_fuel_cost']) && $request['has_fuel_cost'])
            $query->whereNotNull('fuel_cost')->where('fuel_cost', '>', 0);

        // Filter có chi phí bảo dưỡng
        if (isset($request['has_maintenance_cost']) && $request['has_maintenance_cost'])
            $query->whereNotNull('maintenance_cost')->where('maintenance_cost', '>', 0);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'destination',
                'work_content',
                'note',
                'approval_note',
                'rejection_note',
                'current_km',
                'return_km',
                'fuel_cost',
                'maintenance_cost',
            ],
            'date' => [
                'estimated_vehicle_return_date',
            ],
            'datetime' => [
                'vehicle_pickup_time',
            ],
            'relations' => [
                'vehicle' => ['brand', 'license_plate'],
                'createdBy' => ['name'],
                'approvedBy' => ['name'],
                'fuelCostPaidBy' => ['name'],
                'maintenanceCostPaidBy' => ['name'],
            ]
        ];
    }

    public function statistic(array $request)
    {
        $query = $this->model->whereIn('status', [
            'approved',
            'returned',
        ]);
        if (isset($request['year']))
            $query->whereYear('created_at', $request['year']);
        if (isset($request['month']))
            $query->whereMonth('created_at', $request['month']);

        $total_loans = (clone $query)->count();

        $not_returned = (clone $query)
            ->where('status', 'approved')
            ->whereNull('returned_at')
            ->count();

        $returned_not_ready = (clone $query)
            ->where('status', 'returned')
            ->whereNotNull('vehicle_status_return')
            ->where('vehicle_status_return', '!=', 'ready')
            ->with($this->relations)
            ->get();

        // Tổng chi phí xăng
        $total_fuel_cost = (clone $query)
            ->where('status', 'returned')
            ->sum('fuel_cost');

        // Tổng chi phí bảo dưỡng
        $total_maintenance_cost = (clone $query)
            ->where('status', 'returned')
            ->sum('maintenance_cost');

        return [
            'total_loans' => [
                'original' => 'total_loans',
                'converted' => 'Tổng lượt mượn',
                'color' => 'primary',
                'icon' => 'ti ti-arrow-forward-up',
                'value' => $total_loans,
            ],
            'not_returned' => [
                'original' => 'not_returned',
                'converted' => 'Đang mượn (chưa trả)',
                'color' => 'warning',
                'icon' => 'ti ti-clock-hour-4',
                'value' => $not_returned,
            ],
            'returned_not_ready_count' => [
                'original' => 'returned_not_ready_count',
                'converted' => 'Trả về chưa rửa/lỗi/hỏng',
                'color' => 'pink',
                'icon' => 'ti ti-alert-triangle',
                'value' => $returned_not_ready->count(),
            ],
            'total_fuel_cost' => [
                'original' => 'total_fuel_cost',
                'converted' => 'Tổng chi phí xăng',
                'color' => 'info',
                'icon' => 'ti ti-gas-station',
                'value' => $total_fuel_cost,
            ],
            'total_maintenance_cost' => [
                'original' => 'total_maintenance_cost',
                'converted' => 'Tổng chi phí bảo dưỡng',
                'color' => 'success',
                'icon' => 'ti ti-tool',
                'value' => $total_maintenance_cost,
            ],
            'returned_not_ready_detail' => $returned_not_ready,
        ];
    }

    // Thống kê theo tháng
    public function statisticByMonth(array $request)
    {
        $query = $this->model->whereIn('status', [
            'approved',
            'returned',
        ]);

        if (isset($request['year']))
            $query->whereYear('created_at', $request['year']);
        if (isset($request['month']))
            $query->whereMonth('created_at', $request['month']);

        return $query
            ->selectRaw('
            MONTH(created_at) as month,
            COUNT(*) as total,
            SUM(CASE WHEN status = "returned" THEN fuel_cost ELSE 0 END) as total_fuel_cost,
            SUM(CASE WHEN status = "returned" THEN maintenance_cost ELSE 0 END) as total_maintenance_cost,
            SUM(CASE WHEN status = "returned" THEN (return_km - current_km) ELSE 0 END) as total_km
        ')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($item) => [
                'month' => $item->month,
                'total' => $item->total,
                'total_fuel_cost' => $item->total_fuel_cost ?? 0,
                'total_maintenance_cost' => $item->total_maintenance_cost ?? 0,
                'total_km' => $item->total_km ?? 0,
            ]);
    }

    // Top xe được mượn nhiều nhất
    public function topVehicles(array $request, int $limit = 5)
    {
        $query = $this->model->whereIn('status', [
            'approved',
            'returned',
        ]);

        if (isset($request['year']))
            $query->whereYear('created_at', $request['year']);
        if (isset($request['month']))
            $query->whereMonth('created_at', $request['month']);

        return $query
            ->selectRaw('vehicle_id, COUNT(*) as total')
            ->groupBy('vehicle_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->with('vehicle:id,brand,license_plate')
            ->get()
            ->map(fn($item) => [
                'vehicle_name' => $item->vehicle ? "{$item->vehicle->brand} - {$item->vehicle->license_plate}" : 'N/A',
                'total' => $item->total,
            ]);
    }
}
