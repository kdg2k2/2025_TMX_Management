<?php

namespace App\Repositories;

use App\Models\VehicleLoan;

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
        if (isset($request['vehicle_status_return']))
            $query->where('vehicle_status_return', $request['vehicle_status_return']);
        if (isset($request['created_by']))
            $query->where('created_by', $request['created_by']);
        if (isset($request['vehicle_id']))
            $query->where('vehicle_id', $request['vehicle_id']);
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

        // Số lượt mượn
        $total_loans = (clone $query)->count();

        // Số lượt chưa trả (approved và chưa có returned_at)
        $not_returned = (clone $query)
            ->where('status', 'approved')
            ->whereNull('returned_at')
            ->count();

        // Số lượt trả nhưng phương tiện không ready
        $returned_not_ready = (clone $query)
            ->where('status', 'returned')
            ->whereNotNull('vehicle_status_return')
            ->where('vehicle_status_return', '!=', 'ready')
            ->with($this->relations)
            ->get();

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
            'returned_not_ready_detail' => $returned_not_ready,
        ];
    }
}
