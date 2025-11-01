<?php

namespace App\Services;

use App\Repositories\WorkTimesheetDetailRepository;

class WorkTimesheetDetailService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(WorkTimesheetDetailRepository::class);
    }

    public function getMaxProposedWorkDayInMonth(int $userId, int $month, int $year)
    {
        return $this->tryThrow(function () use ($userId, $month, $year) {
            return $this->repository->getMaxProposedWorkDayInMonth($userId, $month, $year);
        });
    }

    public function getTotalLeaveDaysWithPermission(int $userId, int $year)
    {
        return $this->tryThrow(function () use ($userId, $year) {
            return $this->repository->getTotalLeaveDaysWithPermission($userId, $year);
        });
    }
}
