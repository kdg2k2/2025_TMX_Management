<?php

namespace App\Console\Commands;

use App\Services\DeviceLoanService;
use Illuminate\Console\Command;

class RemindReturnDevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remind-return-device';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nhắc nhở trả thiết bị';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = app(DeviceLoanService::class)->remindReturnDevice();
        $this->info("Đã nhắc nhở $count bản ghi");
    }
}
