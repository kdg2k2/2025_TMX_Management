<?php

namespace App\Repositories;

use App\Models\KasperskyCodeRegistration;

class KasperskyCodeRegistrationRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new KasperskyCodeRegistration();
        $this->relations = [
            'device' => fn($q) => $q->with(['deviceType:id,name'])->select(['id', 'name', 'code']),
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
}
