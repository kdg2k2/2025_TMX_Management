<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class QueueRunDrive extends Command
{
    protected $signature = 'queue:run-drive';
    protected $description = 'Process Drive upload queue';

    public function handle()
    {
        $this->call('queue:work', [
            '--queue' => 'drive-uploads',
            '--stop-when-empty' => true,
            '--tries' => 0,
        ]);

        return Command::SUCCESS;
    }
}
