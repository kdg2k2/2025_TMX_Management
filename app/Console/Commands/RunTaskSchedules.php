<?php

namespace App\Console\Commands;

use Exception;
use App\Models\TaskSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\TaskScheduleService;

class RunTaskSchedules extends Command
{
    protected $signature = 'task-schedules:run';
    protected $description = 'Run all active task schedules that are due';

    public function handle(TaskScheduleService $service): int
    {
        $schedules = TaskSchedule::where('is_active', true)
            ->whereNotNull('cron_expression')
            ->where('manual_run', true)
            ->where('next_run_at', '<=', now())
            ->get();

        foreach ($schedules as $schedule) {
            try {
                $this->info("Running schedule: {$schedule->name}");
                $service->run($schedule->code);
            } catch (Exception $e) {
                Log::error("Run task schedules {$schedule->code} error: {$e->getMessage()}");
                continue;
            }
        }

        $this->info("Completed running {$schedules->count()} schedules");
        return 0;
    }
}
