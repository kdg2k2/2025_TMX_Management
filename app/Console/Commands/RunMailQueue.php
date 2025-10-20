<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunMailQueue extends Command
{
    protected $signature = 'queue:run-mail';
    protected $description = 'Run mail queue jobs';

    public function handle()
    {
        // Chỉ xử lý queue 'emails', không xử lý queue 'default' hay queue khác
        Artisan::call('queue:work --stop-when-empty --queue=emails');
        $this->info('Email queue processed.');
    }
}
