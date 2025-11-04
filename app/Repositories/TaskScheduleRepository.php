<?php

namespace App\Repositories;

use App\Models\TaskSchedule;

class TaskScheduleRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new TaskSchedule();
        $this->relations = [
            'users:id,name',
        ];
    }

    public function getFrequency($key = null)
    {
        return $this->model->getFrequency($key);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'cron_expression',
                'frequency',
                'content',
                'subject',
                'description',
                'name',
            ],
            'date' => [],
            'datetime' => [
                'last_run_at',
                'next_run_at',
            ],
            'relations' => []
        ];
    }
}
