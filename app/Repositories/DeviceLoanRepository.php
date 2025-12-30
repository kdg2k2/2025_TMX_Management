<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\DeviceLoan;

class DeviceLoanRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new DeviceLoan();
        $this->relations = [
            'device' => app(DeviceRepository::class)->relations,
            'createdBy:id,name,path',
            'approvedBy:id,name,path',
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
        if (isset($request['device_id']))
            $query->where('device_id', $request['device_id']);
        if (isset($request['device_status_return']))
            $query->where('device_status_return', $request['device_status_return']);
        if (isset($request['status']))
            $query->where('status', $request['status']);
        if (isset($request['created_by']))
            $query->where('created_by', $request['created_by']);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'use_location',
                'approval_note',
                'rejection_note',
                'note',
            ],
            'date' => [
                'borrowed_date',
                'expected_return_at',
            ],
            'datetime' => [
                'approved_at',
                'returned_at',
            ],
            'relations' => [
                'device' => 'name',
                'createdBy' => 'name',
                'approvedBy' => 'name',
            ]
        ];
    }

    public function getOverdueApprovedLoans()
    {
        return $this
            ->model
            ->where('status', 'approved')
            ->whereNull('returned_at')
            ->whereDate('expected_return_at', '<', Carbon::today())
            ->with($this->relations)
            ->get();
    }
}
