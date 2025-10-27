<?php

namespace App\Console\Commands;

use App\Services\GoogleDriveService;
use Illuminate\Console\Command;

class InitFolderOnGoogleDrive extends Command
{
    protected $signature = 'app:init-folder-on-google-drive';
    protected $description = 'Khởi tạo cấu trúc folder trên Google Drive';

    public function handle()
    {
        $this->info('🚀 Bắt đầu khởi tạo cấu trúc folder trên Google Drive...');
        $this->newLine();

        // Cấu trúc folder cần tạo
        $structure = config('google-drive.init_folder');

        try {
            // Gọi service
            $service = app(GoogleDriveService::class);
            $result = $service->initFolders($structure);

            // Hiển thị kết quả dạng bảng
            $tableData = collect($result)->map(function ($item, $path) {
                return [
                    'Path' => $path,
                    'Folder ID' => $item['id'],
                ];
            })->values()->toArray();

            $this->table(
                ['Path', 'Folder ID'],
                $tableData
            );

            $this->newLine();
            $this->info('✅ Hoàn thành! Đã tạo ' . count($result) . ' folder.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Lỗi: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());

            return Command::FAILURE;
        }
    }
}
