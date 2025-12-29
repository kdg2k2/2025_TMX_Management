<?php

namespace App\Console\Commands;

use App\Models\TaskSchedule;
use App\Services\TaskScheduleService;
use Illuminate\Console\Command;

class RunTaskSchedules extends Command
{
    protected $signature = 'task-schedules:run';
    protected $description = 'Run all active task schedules that are due';

    public function handle(TaskScheduleService $service): int
    {
        $schedules = TaskSchedule::where('is_active', true)
            ->whereNotNull('cron_expression')
            ->where('manual_run', true)
            // ->where('next_run_at', '<=', now())
            ->get();

        foreach ($schedules as $schedule) {
            $this->info("Running schedule: {$schedule->name}");
            $service->run($schedule->code);
        }

        $this->info("Completed running {$schedules->count()} schedules");
        return 0;
    }
}
