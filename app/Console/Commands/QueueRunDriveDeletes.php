<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class QueueRunDriveDeletes extends Command
{
    protected $signature = 'queue:run-drive-deletes';
    protected $description = 'Process Drive file deletion queue';

    public function handle()
    {
        $this->call('queue:work', [
            '--queue' => 'drive-deletes',
            '--stop-when-empty' => true,
            '--tries' => 3,
        ]);

        return Command::SUCCESS;
    }
}
