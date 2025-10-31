<?php

namespace App\Repositories;

use App\Models\WorkTimesheet;

class WorkTimesheetRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new WorkTimesheet();
        $this->relations = [
            'details',
        ];
    }
}
