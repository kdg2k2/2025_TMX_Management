<?php

namespace App\Console\Commands;

use App\Services\KasperskyCodeService;
use Illuminate\Console\Command;

class CheckExpiredKasperskyCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expired-kaspersky-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra và cập nhật trạng thái hết hạn cho các mã Kaspersky';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        app(KasperskyCodeService::class)->checkExpiredKasperskyCodes();
    }
}
