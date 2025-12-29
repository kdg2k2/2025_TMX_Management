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
        $schedule
            ->command('queue:work database --queue=high,default,low --once --timeout=300 --tries=3')
            ->everyFiveSeconds()
            ->withoutOverlapping()  // ← Không truyền số = lock đến khi xong
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
        $schedule->command('task-schedules:run')->everyMinute();

        $schedule->command('app:update-contract-g-g-drive-link')->dailyAt('00:00');
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
