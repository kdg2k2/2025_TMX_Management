<?php

namespace App\Repositories;

use App\Models\WorkTimesheetOvertimeDetail;

class WorkTimesheetOvertimeDetailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new WorkTimesheetOvertimeDetail();
        $this->relations = [
            'workTimesheetOvertime',
            'user',
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['month']) && isset($request['year'])) {
            $query->whereHas('workTimesheetOvertime.workTimesheet', function ($q) use ($request) {
                $q
                    ->where('month', $request['month'])
                    ->where('year', $request['year']);
            });
        }

        $query->whereHas('user', fn($q) => $q->where('department_id', auth()->user()->department_id));

        return $query;
    }
}
