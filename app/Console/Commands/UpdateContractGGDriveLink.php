<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Services\ContractService;
use Illuminate\Console\Command;

class UpdateContractGGDriveLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-contract-g-g-drive-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contractService = app(ContractService::class);
        $googleDriveService = app(\App\Services\GoogleDriveService::class);

        Contract::whereNull('ggdrive_link')->chunk(50, function ($contracts) use ($contractService, $googleDriveService) {
            foreach ($contracts as $item) {
                try {
                    $folderPath = $contractService->getFolderOnGoogleDrive($item);
                    $folderId = $googleDriveService->getFolderIdByPath($folderPath);

                    if (!$folderId) {
                        $this->warn("Không tìm được folderId của {$item->short_name}");
                        continue;
                    }

                    $shareLink = data_get(
                        $googleDriveService->getFolderShareLink($folderId),
                        'share_link'
                    );

                    $item->update([
                        'ggdrive_link' => $shareLink
                    ]);

                    $this->info("✔ Đã cập nhật link cho {$item->short_name}");
                    sleep(5);  // tránh Google Drive rate limit
                } catch (\Throwable $e) {
                    $this->error("❌ Lỗi {$item->short_name}: " . $e->getMessage());
                }
            }
        });
    }
}
