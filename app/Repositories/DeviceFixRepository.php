<?php

namespace App\Repositories;

use App\Models\DeviceFix;

class DeviceFixRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new DeviceFix();
        $this->relations = [
            'device' => app(DeviceRepository::class)->relations,
            'createdBy:id,name,path',
            'approvedBy:id,name,path',
        ];
    }

    public function getStatus($key = null)
    {
        return $this->model->getStatus($key);
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['device_id']))
            $query->where('device_id', $request['device_id']);
        if (isset($request['status']))
            $query->where('status', $request['status']);
        if (isset($request['created_by']))
            $query->where('created_by', $request['created_by']);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'suggested_content',
                'device_status',
                'approval_note',
                'rejection_note',
                'note',
            ],
            'date' => [],
            'datetime' => [
                'approved_at',
                'fixed_at',
            ],
            'relations' => [
                'device' => 'name',
                'createdBy' => 'name',
                'approvedBy' => 'name',
            ]
        ];
    }
}
