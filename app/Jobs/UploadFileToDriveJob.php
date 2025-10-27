<?php

namespace App\Jobs;

use App\Services\GoogleDriveService;
use App\Services\HandlerUploadFileService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UploadFileToDriveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 0;  // Thử vô tận (0 = unlimited)
    public $retryAfter = 5;  // Sau 5 giây mới thử lại
    public $maxExceptions = 0;  // Không giới hạn số exception

    protected $localFilePath;
    protected $driveFolderPath;
    protected $customFileName;
    protected $deleteAfterUpload;
    protected $overwrite;

    /**
     * @param string $localFilePath - Đường dẫn tuyệt đối đến file local
     * @param string $driveFolderPath - Path folder trên Drive (VD: "A/01/11")
     * @param string|null $customFileName - Tên file tùy chỉnh (null = giữ tên gốc)
     * @param bool $deleteAfterUpload - Xóa file local sau khi upload thành công
     * @param bool $overwrite - Ghi đè nếu file đã tồn tại
     */
    public function __construct(
        string $localFilePath,
        string $driveFolderPath,
        ?string $customFileName = null,
        bool $deleteAfterUpload = false,
        bool $overwrite = false
    ) {
        $this->localFilePath = $localFilePath;
        $this->driveFolderPath = $driveFolderPath;
        $this->customFileName = $customFileName;
        $this->deleteAfterUpload = $deleteAfterUpload;
        $this->overwrite = $overwrite;

        // Đặt queue
        $this->onQueue('drive-uploads');
    }

    public function handle()
    {
        $service = app(GoogleDriveService::class);

        $result = $service->uploadFileByPath(
            $this->localFilePath,
            $this->driveFolderPath,
            $this->customFileName,
            $this->overwrite
        );

        if ($result['success']) {
            Log::info('File uploaded to Drive successfully', [
                'local_path' => $this->localFilePath,
                'drive_path' => $this->driveFolderPath,
                'file_id' => $result['file_id'],
                'file_name' => $result['file_name'],
                'action' => $result['action']
            ]);

            // Xóa file local nếu cần
            if ($this->deleteAfterUpload) {
                app(HandlerUploadFileService::class)->removeFiles($this->localFilePath);
                Log::info('Local file deleted after upload', [
                    'local_path' => $this->localFilePath
                ]);
            }
        } else {
            throw new \Exception($result['message'] ?? 'Upload failed');
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
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
