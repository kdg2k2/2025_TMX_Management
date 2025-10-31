<?php

namespace App\Repositories;

use App\Models\WorkTimesheetDetail;

class WorkTimesheetDetailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new WorkTimesheetDetail();
        $this->relations = [];
    }

    public function getByUserIdAndMonthYear(int $userId, int $month, int $year)
    {
        return $this->model->where('user_id', $userId)->whereHas('workTimesheet', fn($q) => $q->where('month', $month)->where('year', $year))->first();
    }
}
