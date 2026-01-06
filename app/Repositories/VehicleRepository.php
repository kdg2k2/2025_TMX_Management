<?php

namespace App\Repositories;

use App\Models\Vehicle;
use Carbon\Carbon;

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

    public function statistic()
    {
        return $this->model->selectRaw("
            SUM(status = 'ready')        AS `ready`,
            SUM(status = 'unwashed')     AS `unwashed`,
            SUM(status = 'broken')       AS `broken`,
            SUM(status = 'faulty')       AS `faulty`,
            SUM(status = 'lost')         AS `lost`,
            SUM(status = 'loaned')       AS `loaned`
        ")->first()->toArray() ?? [];
    }

    protected function getExpiredByColumn(string $column, int $days)
    {
        $fromDate = Carbon::now();
        $toDate = Carbon::now()->addDays($days);

        return $this
            ->model
            ->whereDate($column, '<=', $toDate)
            ->whereDate($column, '>=', $fromDate)
            ->get();
    }

    public function getInspectionExpiryWarnings(int $days = 10)
    {
        return $this->getExpiredByColumn('inspection_expired_at', $days);
    }

    public function getLiabilityInsuranceExpiryWarnings(int $days = 10)
    {
        return $this->getExpiredByColumn('liability_insurance_expired_at', $days);
    }

    public function getBodyInsuranceExpiryWarnings(int $days = 10)
    {
        return $this->getExpiredByColumn('body_insurance_expired_at', $days);
    }

    public function getExpiryWarnings(int $days = 10)
    {
        return [
            'inspection' => $this->getInspectionExpiryWarnings($days),
            'liability_insurance' => $this->getLiabilityInsuranceExpiryWarnings($days),
            'body_insurance' => $this->getBodyInsuranceExpiryWarnings($days),
        ];
    }

    public function getVehiclesNearMaintenance(int $warningKm = 200)
    {
        return $this
            ->model
            ->whereNotNull('maintenance_km')
            ->whereRaw('(maintenance_km - current_km) <= ?', [$warningKm])
            ->whereRaw('(maintenance_km - current_km) >= 0')
            ->get();
    }
}
