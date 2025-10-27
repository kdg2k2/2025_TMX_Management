<?php

namespace App\Jobs;

use App\Services\GoogleDriveService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InitFoldersOnDriveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 0;
    public $retryAfter = 5;
    public $maxExceptions = 0;

    protected $structure;
    protected $rootParentId;
    protected $jobName;

    /**
     * @param array $structure - Cấu trúc folder dạng mảng đa cấp
     * @param string|null $rootParentId - ID folder cha (null = root Drive)
     * @param string|null $jobName - Tên job để dễ phân biệt trong log
     */
    public function __construct(
        array $structure,
        ?string $rootParentId = null,
        ?string $jobName = null
    ) {
        $this->structure = $structure;
        $this->rootParentId = $rootParentId;
        $this->jobName = $jobName ?? 'InitFolders-' . now()->format('YmdHis');

        $this->onQueue('drive-folders');
    }

    public function handle()
    {
        $service = app(GoogleDriveService::class);

        Log::info("[{$this->jobName}] Bắt đầu khởi tạo folders", [
            'structure' => $this->structure,
            'root_parent_id' => $this->rootParentId
        ]);

        $results = $service->initFolders($this->structure, $this->rootParentId);

        Log::info("[{$this->jobName}] Hoàn thành khởi tạo folders", [
            'total_folders' => count($results),
            'results' => $results
        ]);

        return $results;
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
            10,     // 10 giây
            20,     // 20 giây
            40,     // 40 giây
            60,     // 1 phút
            300,    // 5 phút
            600,    // 10 phút
            1800,   // 30 phút
        ];
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception)
    {
        Log::error("[{$this->jobName}] InitFoldersOnDriveJob failed permanently", [
            'structure' => $this->structure,
            'root_parent_id' => $this->rootParentId,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
