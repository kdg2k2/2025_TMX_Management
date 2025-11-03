<?php

namespace App\Services;

use App\Repositories\WorkTimesheetOvertimeDetailRepository;

class WorkTimesheetOvertimeDetailService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(WorkTimesheetOvertimeDetailRepository::class);
    }
}
