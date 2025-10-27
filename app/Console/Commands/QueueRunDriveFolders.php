<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class QueueRunDriveFolders extends Command
{
    protected $signature = 'queue:run-drive-folders';
    protected $description = 'Process Drive folder creation queue';

    public function handle()
    {
        $this->call('queue:work', [
            '--queue' => 'drive-folders',
            '--stop-when-empty' => true,
            '--tries' => 0,
        ]);

        return Command::SUCCESS;
    }
}
