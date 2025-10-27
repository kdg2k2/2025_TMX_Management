<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitFolderOnGoogleDrive extends Command
{
    protected $signature = 'app:init-folder-on-google-drive {--sync : Chạy đồng bộ không dùng queue}';
    protected $description = 'Khởi tạo cấu trúc folder trên Google Drive';

    public function handle()
    {
        // Cấu trúc folder cần tạo
        $structure = config('google-drive.init_folder');

        if ($this->option('sync')) {
            // Chạy đồng bộ
            $this->info('🚀 Chạy đồng bộ...');

            try {
                $service = app(\App\Services\GoogleDriveService::class);
                $results = $service->initFolders($structure);

                $this->newLine();
                $this->table(
                    ['Path', 'Folder ID'],
                    collect($results)->map(fn($item, $path) => [$path, $item['id']])->values()
                );

                $this->newLine();
                $this->info('✅ Hoàn thành! Đã tạo ' . count($results) . ' folder.');

                return Command::SUCCESS;
            } catch (\Exception $e) {
                $this->error('❌ Lỗi: ' . $e->getMessage());
                return Command::FAILURE;
            }
        } else {
            // Đưa vào queue
            $this->info('🚀 Đã đưa job vào queue...');

            \App\Jobs\InitFoldersOnDriveJob::dispatch($structure, null, 'InitFolders-Manual');

            $this->info('✅ Job đã được thêm vào queue "drive-folders"');
            $this->info('💡 Xem log để theo dõi tiến trình: tail -f storage/logs/laravel.log');

            return Command::SUCCESS;
        }
    }
}
