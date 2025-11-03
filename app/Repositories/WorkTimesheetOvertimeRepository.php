<?php

namespace App\Repositories;

use App\Models\WorkTimesheetOvertime;

class WorkTimesheetOvertimeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new WorkTimesheetOvertime();
        $this->relations = [
            'details.user:id,name',
        ];
    }

    public function findByMonthYear(int $month, int $year)
    {
        return $this->model->whereHas('workTimesheet', fn($q) => $q->where('month', $month)->where('year', $year))->first();
    }
}
