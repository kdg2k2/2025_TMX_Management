<?php

namespace App\Repositories;

use App\Models\WorkSchedule;

class WorkScheduleRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new WorkSchedule();
        $this->relations = [
            'createdBy',
            'approvedBy',
            'returnApprovedBy',
            'contract:id,name',
        ];
    }

    public function getTypeProgram($key = null)
    {
        return $this->model->getTypeProgram($key);
    }

    public function getApprovalStatus($key = null)
    {
        return $this->model->getApprovalStatus($key);
    }

    public function getReturnApprovalStatus($key = null)
    {
        return $this->model->getReturnApprovalStatus($key);
    }

    public function getIsCompleted($key = null)
    {
        return $this->model->getIsCompleted($key);
    }

    public function getSearchConfig(): array
    {
        return [
            'text' => [
                'address',
                'content',
                'other_program',
                'clue',
                'participants',
                'note',
                'approval_note',
                'return_approval_note',
                'total_trip_days',
                'total_work_days',
            ],
            'date' => [
                'from_date',
                'to_date',
                'approval_date',
                'return_approval_date',
            ],
            'datetime' => [
                'return_datetime',
            ],
            'relations' => [
                'createdBy' => ['name'],
                'approvedBy' => ['name'],
                'returnApprovedBy' => ['name'],
                'contract' => ['name'],
            ]
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        foreach ([
            'created_by',
            'type_program',
            'contract_id',
            'approval_status',
            'return_approval_status',
            'is_completed',
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

    public function getUserWorkScheduleFromTo(int $userId, string $from, string $to)
    {
        return $this
            ->model
            ->where('created_by', $userId)
            ->where(function ($q) use ($from, $to) {
                $q
                    ->where('from_date', '<=', $to)
                    ->where('to_date', '>=', $from);
            })
            ->get(['from_date', 'to_date']);
    }
}
