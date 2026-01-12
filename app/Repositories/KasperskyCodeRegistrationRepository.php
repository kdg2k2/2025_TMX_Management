<?php

namespace App\Repositories;

use App\Models\KasperskyCodeRegistration;
use App\Services\KasperskyCodeService;

class KasperskyCodeRegistrationRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new KasperskyCodeRegistration();
        $this->relations = [
            'device' => fn($q) => $q->with([
                'deviceType:id,name'
            ])->select(
                [
                    'id',
                    'name',
                    'code',
                    'device_type_id'
                ]
            ),
            'createdBy:id,name',
            'approvedBy:id,name',
            'codes' => fn($q) => $q->select([
                'kaspersky_codes.id',
                'total_quantity',
                'used_quantity',
                'valid_days',
                'is_quantity_exceeded',
                'is_expired',
                'started_at',
                'expired_at',
            ]),
        ];
    }

    public function getType($key = null)
    {
        return $this->model->getType($key);
    }

    public function getStatus($key = null)
    {
        return $this->model->getStatus($key);
    }

    public function getSearchConfig(): array
    {
        return [
            'text' => [],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'device' => ['name'],
                'createdBy' => ['name'],
                'approvedBy' => ['name'],
            ]
        ];
    }

    public function checkUniqueDeviceRegistration(int $deviceId)
    {
        return $this->model->where('status', 'pending')->where('device_id', $deviceId)->exists();
    }

    public function registrationApprovedIsntExpired()
    {
        return $this
            ->model
            ->where('status', 'approved')
            ->with(['codes' => function ($query) {
                $query
                    ->where('is_expired', false)
                    ->where(function ($q) {
                        $q
                            ->whereNull('expired_at')
                            ->orWhere('expired_at', '>', now());
                    });
            }])
            ->get();
    }

    public function statistic(array $request = [])
    {
        $relations = $this->relations;
        $relations['codes'] = function ($q) {
            $q->select([
                'kaspersky_codes.id',
                'code',
                'total_quantity',
                'used_quantity',
                'available_quantity',
                'valid_days',
                'is_expired',
                'started_at',
                'expired_at',
            ])->selectRaw('
                CASE
                    WHEN expired_at IS NULL THEN NULL
                    WHEN expired_at < CURDATE() THEN 0
                    ELSE DATEDIFF(expired_at, CURDATE())
                END as days_remaining
            ');
        };

        $query = $this->model->where('status', 'approved');

        if (isset($request['year']))
            $query->whereYear('created_at', $request['year']);
        if (isset($request['month']))
            $query->whereMonth('created_at', $request['month']);

        return $query
            ->orderByDesc('id')
            ->orderByDesc('approved_at')
            ->with($relations)
            ->get();
    }
}
