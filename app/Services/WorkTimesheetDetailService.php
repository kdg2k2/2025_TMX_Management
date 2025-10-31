<?php

namespace App\Services;

use App\Repositories\WorkTimesheetDetailRepository;

class WorkTimesheetDetailService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(WorkTimesheetDetailRepository::class);
    }

    public function getByUserIdAndMonthYear(int $userId, int $month, int $year)
    {
        return $this->tryThrow(fn() => $this->repository->getByUserIdAndMonthYear($userId, $month, $year));
    }
}
