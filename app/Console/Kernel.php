<?php

namespace App\Console;

use App\Services\WorkScheduleService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $this->tasks($schedule);
        $this->queueJobs($schedule);
    }

    private function queueJobs(Schedule $schedule)
    {
        $workerMaxTime = 3600;  // 60 phút
        $lockBuffer = 5;  // 5 phút buffer
        $lockMinutes = ($workerMaxTime / 60) + $lockBuffer;  // 65 phút

        $schedule
            ->command("queue:work database --queue=high,default,low --max-time={$workerMaxTime} --sleep=3 --tries=3 --timeout=300")
            ->hourly()
            ->withoutOverlapping($lockMinutes)
            ->runInBackground();

        // Retry failed jobs mỗi 6 giờ
        $schedule
            ->command('queue:retry all')
            ->everySixHours()
            ->withoutOverlapping(10)
            ->before(function () {
                logger('[Schedule] Manual retry of failed jobs (every 6h)');
            });

        // Dọn dẹp failed jobs cũ hơn 2 tuần
        $schedule
            ->command('queue:prune-failed --hours=336')
            ->daily()
            ->at('03:00')
            ->withoutOverlapping(30)
            ->before(function () {
                logger('[Schedule] Cleaning up failed jobs older than 2 weeks');
            });
    }

    private function tasks(Schedule $schedule)
    {
        $schedule
            ->call(function () {
                app(WorkScheduleService::class)->setCompletedWorkSchedules();
            })
            ->name('set-completed-work-schedules')
            ->dailyAt('17:00')
            ->withoutOverlapping();

        $schedule->command('task-schedules:run')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
