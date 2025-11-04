<?php

namespace App\Repositories;

use App\Models\TaskSchedule;

class TaskScheduleRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new TaskSchedule();
        $this->relations = [];
    }
}