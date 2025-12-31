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
        if (isset($request['statuses']))
            $query->whereIn('current_status', $request['statuses']);
    }

    public function statistic()
    {
        return $this->model->selectRaw("
            SUM(current_status = 'normal')        AS `normal`,
            SUM(current_status = 'broken')        AS `broken`,
            SUM(current_status = 'faulty')        AS `faulty`,
            SUM(current_status = 'lost')          AS `lost`,
            SUM(current_status = 'loaned')        AS `loaned`,
            SUM(current_status = 'under_repair')  AS `under_repair`,
            SUM(current_status = 'stored')        AS `stored`
        ")->first()->toArray() ?? [];
    }

    public function statisticByType()
    {
        return $this
            ->model
            ->selectRaw('device_type_id, COUNT(*) as total')
            ->groupBy('device_type_id')
            ->with('deviceType:id,name')
            ->get()
            ->map(fn($item) => [
                'device_type_id' => $item->device_type_id,
                'device_type_name' => $item->deviceType->name ?? 'N/A',
                'total' => $item->total,
            ]);
    }

    public function statisticStatusByType()
    {
        return $this
            ->model
            ->selectRaw("
            device_type_id,
            SUM(current_status = 'normal') as `normal`,
            SUM(current_status = 'broken') as `broken`,
            SUM(current_status = 'faulty') as `faulty`,
            SUM(current_status = 'lost') as `lost`,
            SUM(current_status = 'loaned') as `loaned`,
            SUM(current_status = 'under_repair') as `under_repair`,
            SUM(current_status = 'stored') as `stored`
        ")
            ->groupBy('device_type_id')
            ->with('deviceType:id,name')
            ->get();
    }
}
