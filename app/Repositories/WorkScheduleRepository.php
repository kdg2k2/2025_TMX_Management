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
            'endApprovedBy',
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

    public function getEndApprovalStatus($key = null)
    {
        return $this->model->getEndApprovalStatus($key);
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
                'endApprovedBy' => ['name'],
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
    }
}
