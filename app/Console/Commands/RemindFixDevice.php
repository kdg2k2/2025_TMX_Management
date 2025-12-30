<?php

namespace App\Console\Commands;

use App\Services\DeviceFixService;
use Illuminate\Console\Command;

class RemindFixDevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remind-fix-device';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nhắc nhở sửa thiết bị';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = app(DeviceFixService::class)->remindFixDevice() ?? 0;
        $this->info("Đã nhắc nhở sửa thiết bị $count bản ghi");
    }
}
