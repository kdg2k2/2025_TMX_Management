<?php

namespace App\Repositories;

use App\Models\Device;

class DeviceRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Device();
        $this->relations = [
            'deviceType:id,name',
            'user:id,name,path',
            'images'
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
                'name',
                'code',
                'seri',
                'current_location',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'deviceType' => ['name'],
                'user' => ['name'],
            ]
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['device_type_id']))
            $query->where('device_type_id', $request['device_type_id']);
        if (isset($request['current_status']))
            $query->where('current_status', $request['current_status']);
    }
}
