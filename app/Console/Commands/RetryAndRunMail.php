<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RetryAndRunMail extends Command
{
    protected $signature = 'mail:retry-and-run';
    protected $description = 'Retry all failed jobs and then run mail jobs';

    public function handle()
    {
        $this->info('Retrying failed jobs...');
        Artisan::call('queue:retry', ['id' => 'all']);

        $this->info('Running mail jobs...');
        Artisan::call('queue:run-mail');

        $this->info('Mail jobs processed.');
    }
}
