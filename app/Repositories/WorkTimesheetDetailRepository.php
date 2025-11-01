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

    private function baseQueryByUserIdAndYear(int $userId, int $year)
    {
        return $this->model->where('user_id', $userId)->whereHas('workTimesheet', fn($q) => $q->where('year', $year));
    }

    private function baseQueryByUserIdAndMonthYear(int $userId, int $month, int $year)
    {
        return $this->model->where('user_id', $userId)->whereHas('workTimesheet', fn($q) => $q->where('month', $month)->where('year', $year));
    }

    public function getMaxProposedWorkDayInMonth(int $userId, int $month, int $year)
    {
        return $this->baseQueryByUserIdAndMonthYear($userId, $month, $year)->max('proposed_work_days');
    }

    public function getTotalLeaveDaysWithPermission(int $userId, int $year)
    {
        return $this->baseQueryByUserIdAndYear($userId, $year)->max('leave_days_with_permission');
    }
}
