<?php

namespace App\Repositories;

use App\Models\DeviceLoan;
use Carbon\Carbon;

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
                'device' => ['name'],
                'createdBy' => ['name'],
                'approvedBy' => ['name'],
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
            ->get();
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

        // Số lượt trả nhưng thiết bị không normal
        $returned_not_normal = (clone $query)
            ->where('status', 'returned')
            ->whereNotNull('device_status_return')
            ->where('device_status_return', '!=', 'normal')
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
            'returned_not_normal_count' => [
                'original' => 'returned_not_normal_count',
                'converted' => 'Trả về bị lỗi/hỏng',
                'color' => 'pink',
                'icon' => 'ti ti-alert-triangle',
                'value' => $returned_not_normal->count(),
            ],
            'returned_not_normal_detail' => $returned_not_normal,
        ];
    }

    public function statisticByMonth(array $request)
    {
        $query = $this->model->whereIn('status', [
            'approved',
            'returned',
        ]);

        if (isset($request['year']))
            $query->whereYear('created_at', $request['year']);
        if (isset($request['month']))
            $query->whereMonth('created_at', $request['month']);

        return $query
            ->selectRaw('
        MONTH(created_at) as month,
        COUNT(*) as total
    ')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($item) => [
                'month' => $item->month,
                'total' => $item->total,
            ]);
    }
}
