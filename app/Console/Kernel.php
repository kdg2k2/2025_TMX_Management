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
        // Xử lý queue EMAILS mỗi 10 giây
        $schedule
            ->command('queue:run-mail')
            ->everyFiveSeconds()
            ->withoutOverlapping(2)  // 2 phút timeout
            ->after(function () {
                logger('[Schedule] Processed EMAILS queue - SendMailJob only (5s interval)');
            });

        // Xử lý queue DEFAULT mỗi phút - Xử lý các job khác (không phải mail)
        $schedule
            ->command('queue:work --stop-when-empty --queue=default')
            ->everyTenSeconds()
            ->withoutOverlapping(2)  // 2 phút timeout
            ->after(function () {
                logger('[Schedule] Processed DEFAULT queue - non-mail jobs (10s interval)');
            });

        // Retry failed jobs mỗi 6 giờ
        $schedule
            ->command('queue:retry all')
            ->everySixHours()
            ->withoutOverlapping(10)
            ->before(function () {
                logger('[Schedule] Manual retry of truly failed jobs (every 6h)');
            });

        // Dọn dẹp failed jobs cũ hơn 2 tuần
        $schedule
            ->command('queue:prune-failed --hours=336')
            ->daily()
            ->at('03:00')
            ->withoutOverlapping(30)
            ->before(function () {
                logger('[Schedule] Cleaning up failed jobs older than 2 weeks');
            });  // 336h = 2 tuần
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
