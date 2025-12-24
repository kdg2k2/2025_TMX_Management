<?php

namespace App\Jobs;

use App\Services\GoogleDriveService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UploadFileToDriveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 0;
    public $retryAfter = 5;
    public $maxExceptions = 0;

    protected $localFilePath;
    protected $driveFolderPath;
    protected $customFileName;
    protected $deleteAfterUpload;
    protected $overwrite;
    protected $driveFilePathToDelete;

    /**
     * @param string $localFilePath - Đường dẫn file local cần upload
     * @param string $driveFolderPath - Path folder trên Drive (VD: "A/01/11")
     * @param string|null $customFileName - Tên file tùy chỉnh
     * @param bool $deleteAfterUpload - Xóa file local sau khi upload
     * @param bool $overwrite - Ghi đè nếu file đã tồn tại
     * @param string|null $driveFilePathToDelete - Full path file trên Drive cần xóa (VD: "A/01/11/old-file.pdf")
     */
    public function __construct(
        string $localFilePath,
        string $driveFolderPath,
        ?string $customFileName = null,
        bool $deleteAfterUpload = false,
        bool $overwrite = false,
        ?string $driveFilePathToDelete = null
    ) {
        $this->localFilePath = $localFilePath;
        $this->driveFolderPath = $driveFolderPath;
        $this->customFileName = $customFileName;
        $this->deleteAfterUpload = $deleteAfterUpload;
        $this->overwrite = $overwrite;
        $this->driveFilePathToDelete = $driveFilePathToDelete;

        $this->onQueue('high');
    }

    public function handle()
    {
        $service = app(GoogleDriveService::class);

        // 1. Upload file mới
        $result = $service->uploadFileByPath(
            $this->localFilePath,
            $this->driveFolderPath,
            $this->customFileName,
            $this->overwrite
        );

        if (!$result['success']) {
            throw new \Exception($result['message'] ?? 'Upload failed');
        }

        Log::info('File uploaded to Drive successfully', [
            'local_path' => $this->localFilePath,
            'drive_path' => $this->driveFolderPath,
            'file_id' => $result['file_id'],
            'file_name' => $result['file_name'],
            'action' => $result['action']
        ]);

        // 2. Xóa file local nếu cần
        if ($this->deleteAfterUpload && file_exists($this->localFilePath)) {
            unlink($this->localFilePath);
            Log::info('Local file deleted after upload', [
                'local_path' => $this->localFilePath
            ]);
        }

        // 3. Xóa file cũ trên Drive nếu có
        if ($this->driveFilePathToDelete) {
            try {
                $deleteResult = $service->deleteFileByPath($this->driveFilePathToDelete);

                if ($deleteResult['success']) {
                    Log::info('Old file deleted from Drive', [
                        'drive_file_path' => $this->driveFilePathToDelete,
                        'file_id' => $deleteResult['file_id'] ?? null,
                        'file_name' => $deleteResult['file_name'] ?? null
                    ]);
                } else {
                    Log::warning('Old file not found on Drive, skipping deletion', [
                        'drive_file_path' => $this->driveFilePathToDelete,
                        'message' => $deleteResult['message']
                    ]);
                }
            } catch (\Exception $e) {
                // Không throw exception để không ảnh hưởng đến upload
                Log::error('Failed to delete old file from Drive', [
                    'drive_file_path' => $this->driveFilePathToDelete,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Thử trong 1 tuần
     */
    public function retryUntil()
    {
        return now()->addWeek();
    }

    /**
     * Exponential backoff
     */
    public function backoff()
    {
        return [
            10,  // 10 giây
            20,  // 20 giây
            40,  // 40 giây
            60,  // 1 phút
            300,  // 5 phút
            600,  // 10 phút
            1800,  // 30 phút
        ];
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception)
    {
        Log::error('UploadFileToDriveJob failed permanently', [
            'local_path' => $this->localFilePath,
            'drive_path' => $this->driveFolderPath,
            'custom_name' => $this->customFileName,
            'drive_file_to_delete' => $this->driveFilePathToDelete,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
