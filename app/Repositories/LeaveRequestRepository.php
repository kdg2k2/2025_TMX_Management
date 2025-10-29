<?php

namespace App\Repositories;

use App\Models\LeaveRequest;

class LeaveRequestRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new LeaveRequest();
        $this->relations = [
            'createdBy',
            'approvedBy',
            'adjustApprovedBy',
        ];
    }

    public function getType($key = null)
    {
        return $this->model->getType($key);
    }

    public function getApprovalStatus($key = null)
    {
        return $this->model->getApprovalStatus($key);
    }

    public function getAdjustApprovalStatus($key = null)
    {
        return $this->model->getAdjustApprovalStatus($key);
    }

    public function getSearchConfig(): array
    {
        return [
            'text' => [
                'reason',
                'total_leave_days',
                'approval_note',
                'adjust_approval_note',
            ],
            'date' => [
                'from_date',
                'to_date',
                'approval_date',
                'adjust_approval_date',
            ],
            'datetime' => [],
            'relations' => [
                'createdBy' => ['name'],
                'approvedBy' => ['name'],
                'adjustApprovedBy' => ['name'],
            ]
        ];
    }

    public function applyListFilters($query, array $request)
    {
        foreach ([
            'created_by',
            'approval_status',
            'adjust_approval_status',
        ] as $field) {
            if (isset($request[$field]))
                $query->where($field, $request[$field]);
        }

        if (isset($request['from_date']) && isset($request['to_date']))
            $query->where(function ($q) use ($request) {
                $q
                    ->whereBetween('from_date', [$request['from_date'], $request['to_date']])
                    ->orWhereBetween('to_date', [$request['from_date'], $request['to_date']]);
            });
    }

    public function getUserLeaveFromTo(int $userId, string $from, string $to, int $ignoreId = null)
    {
        $query = $this
            ->model
            ->query();

        if (isset($ignoreId))
            $query->whereNot('id', $ignoreId);

        return $query
            ->whereNot('approval_status', 'rejected')
            ->where('created_by', $userId)
            ->where(function ($q) use ($from, $to) {
                $q
                    ->where('from_date', '<=', $to)
                    ->where('to_date', '>=', $from);
            })
            ->get(['from_date', 'to_date']);
    }
}
