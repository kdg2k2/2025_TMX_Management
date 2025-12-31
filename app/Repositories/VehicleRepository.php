<?php

namespace App\Repositories;

use App\Models\Vehicle;

class VehicleRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Vehicle();
        $this->relations = [
            'user:id,name',
        ];
    }

    public function getStatus($key = null)
    {
        return $this->model->getStatus($key);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'brand',
                'license_plate',
                'current_km',
                'maintenance_km',
                'destination'
            ],
            'date' => [
                'inspection_expired_at',
                'liability_insurance_expired_at',
                'body_insurance_expired_at',
            ],
            'datetime' => [],
            'relations' => [
                'user' => ['name'],
            ]
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['status']))
            $query->where('status', $request['status']);
        if (isset($request['statuses']))
            $query->whereIn('status', $request['statuses']);
    }
}
