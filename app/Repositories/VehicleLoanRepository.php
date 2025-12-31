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
}
