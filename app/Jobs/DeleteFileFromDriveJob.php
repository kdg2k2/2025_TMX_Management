<?php

namespace App\Jobs;

use App\Services\GoogleDriveService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteFileFromDriveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $retryAfter = 10;

    protected $filePath;

    /**
     * @param string $filePath - Full path đến file trên Drive (VD: "A/01/11/document.pdf")
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->onQueue('drive-deletes');
    }

    public function handle()
    {
        $service = app(GoogleDriveService::class);

        $result = $service->deleteFileByPath($this->filePath);

        if ($result['success']) {
            Log::info('File deleted from Drive successfully', [
                'file_path' => $this->filePath,
                'file_id' => $result['file_id'] ?? null,
                'file_name' => $result['file_name'] ?? null
            ]);
        } else {
            Log::warning('File not found on Drive, skipping deletion', [
                'file_path' => $this->filePath,
                'message' => $result['message']
            ]);
        }
    }

    public function retryUntil()
    {
        return now()->addDay();
    }

    public function backoff()
    {
        return [10, 30, 60];
    }

    public function failed(\Throwable $exception)
    {
        Log::error('DeleteFileFromDriveJob failed', [
            'file_path' => $this->filePath,
            'exception' => $exception->getMessage()
        ]);
    }
}
