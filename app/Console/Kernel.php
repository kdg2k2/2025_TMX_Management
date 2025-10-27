<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Queue EMAILS mỗi 5 giây
        $schedule
            ->command('queue:run-mail')
            ->everyFiveSeconds()
            ->withoutOverlapping(2)
            ->after(function () {
                logger('[Schedule] Processed EMAILS queue (5s interval)');
            });

        // Queue DRIVE UPLOADS mỗi 10 giây
        $schedule
            ->command('queue:run-drive')
            ->everyTenSeconds()
            ->withoutOverlapping(5)
            ->after(function () {
                logger('[Schedule] Processed DRIVE-UPLOADS queue (10s interval)');
            });

    // Queue DRIVE FOLDERS mỗi 30 giây
    $schedule
        ->command('queue:run-drive-folders')
        ->everyThirtySeconds()
        ->withoutOverlapping(10)
        ->after(function () {
            logger('[Schedule] Processed DRIVE-FOLDERS queue (30s interval)');
        });

        // Queue DEFAULT mỗi 10 giây
        $schedule
            ->command('queue:work --stop-when-empty --queue=default')
            ->everyTenSeconds()
            ->withoutOverlapping(2)
            ->after(function () {
                logger('[Schedule] Processed DEFAULT queue (10s interval)');
            });

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

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
