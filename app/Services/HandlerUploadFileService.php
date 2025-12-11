<?php

namespace App\Services;

use App\Services\BaseService;
use Exception;
use Log;

class HandlerUploadFileService extends BaseService
{
    public function storeAndRemoveOld($file, string $rootFolder, string $folder = null, string $oldPath = null)
    {
        $folderSave = "uploads/$rootFolder";
        if ($folder)
            $folderSave .= "/$folder";
        $destinationPath = $this->getAbsolutePublicPath($folderSave);

        // Nếu là file dạng UploadedFile (từ request upload)
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            $fileName = app(StringHandlerService::class)->createSlug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . date('Y-m-d_H-i-s') . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $fileName);
        }
        // Nếu là file dạng đường dẫn thực tế (Seeder)
        elseif (is_string($file) && file_exists($file)) {
            $fileName = uniqid($folder) . '.' . pathinfo($file, PATHINFO_EXTENSION);
            copy($file, $destinationPath . '/' . $fileName);
        } elseif (filter_var($file, FILTER_VALIDATE_URL)) {
            return $file;
        } else {
            throw new Exception('Định dạng file không hợp lệ.');
        }

        // Xoá file cũ nếu có
        if ($oldPath && file_exists($this->getAbsolutePublicPath($oldPath)))
            $this->removeFiles($oldPath);

        return "$folderSave/$fileName";
    }

    public function getAbsolutePublicPath(string $filePath)
    {
        // Kiểm tra nếu đã là đường dẫn tuyệt đối (Windows hoặc Linux)
        if (
            \Illuminate\Support\Str::startsWith($filePath, public_path()) ||
            preg_match('/^[A-Z]:\\\\/i', $filePath) ||  // Windows: C:\, D:\
            \Illuminate\Support\Str::startsWith($filePath, '/')  // Linux: /home/...
        ) {
            $destinationPath = $filePath;
        } else {
            $destinationPath = public_path($filePath);
        }

        // Chỉ tạo thư mục nếu path không có extension (là folder)
        if (!str_contains(basename($destinationPath), '.')) {
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
        }

        return $destinationPath;
    }

    public function removeFiles(array|string $paths = null)
    {
        if (!$paths)
            return;

        if (!is_array($paths))
            $paths = [$paths];

        foreach ($paths as $p) {
            $this->safeDeleteFile($p);
        }
    }

    public function safeDeleteFile($filePath = null)
    {
        try {
            if (!$filePath)
                return true;

            $fullPath = $this->getAbsolutePublicPath($filePath);

            // Kiểm tra file có tồn tại không
            if (!file_exists($fullPath)) {
                Log::info("File không tồn tại: {$fullPath}");
                return true;  // Coi như đã xóa thành công
            }

            // Kiểm tra quyền đọc/ghi
            if (!is_readable($fullPath) || !is_writable($fullPath)) {
                Log::warning("Không có quyền xóa file: {$fullPath}");

                // Thử chmod để cấp quyền
                if (PHP_OS_FAMILY !== 'Windows') {
                    @chmod($fullPath, 0666);
                }

                // Kiểm tra lại sau khi chmod
                if (!is_writable($fullPath)) {
                    Log::error("Vẫn không thể xóa file sau khi chmod: {$fullPath}");
                    return false;
                }
            }

            // Thử xóa file
            $result = @unlink($fullPath);

            if ($result) {
                Log::info("Xóa file thành công: {$fullPath}");
                return true;
            } else {
                Log::error("Không thể xóa file: {$fullPath}. Error: " . error_get_last()['message'] ?? 'Unknown error');
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Exception khi xóa file {$filePath}: " . $e->getMessage());
            return false;
        }
    }

    public function removeFolder(string $folder)
    {
        $absolutePath = $this->getAbsolutePublicPath($folder);

        if (!is_dir($absolutePath)) {
            \Log::warning("Folder does not exist: {$absolutePath}");
            return false;
        }

        try {
            $this->deleteFolderContents($absolutePath);
            return rmdir($absolutePath);
        } catch (\Exception $e) {
            \Log::error("Failed to remove folder: {$folder}. Error: " . $e->getMessage());
            return false;
        }
    }

    private function deleteFolderContents(string $folder)
    {
        if (!is_dir($folder)) {
            return;
        }

        $files = array_diff(scandir($folder), ['.', '..']);

        foreach ($files as $file) {
            $fullPath = $folder . DIRECTORY_SEPARATOR . $file;

            if (is_dir($fullPath)) {
                $this->removeFolder($fullPath);
            } else {
                $this->removeFiles($fullPath);
            }
        }
    }

    public function uploadChunk(array $request, string $parentFolder)
    {
        $file = $request['file'];  // UploadedFile
        $chunkIndex = (int) $request['dzchunkindex'];  // index của chunk
        $totalChunks = (int) $request['dztotalchunkcount'];
        $fileName = $request['dzfilename'];  // tên file gốc

        // Tạo thư mục lưu các phần chunk
        // uploads/xxx/chunks/filename/0
        $chunkDir = "{$parentFolder}/chunks/{$fileName}";
        $absoluteChunkDir = $this->getAbsolutePublicPath($chunkDir);

        // Lưu chunk vào đúng vị trí
        $file->move($absoluteChunkDir, $chunkIndex);

        // Nếu chưa phải chunk cuối → kết thúc
        if ($chunkIndex < $totalChunks - 1) {
            return [
                'done' => false,
                'message' => "Chunk {$chunkIndex}/{$totalChunks} uploaded"
            ];
        }

        // Chunk cuối → tiến hành merge
        return $this->mergeChunks($parentFolder, $fileName, $totalChunks, $chunkDir);
    }

    private function mergeChunks(string $parentFolder, string $fileName, int $totalChunks, string $chunkDir)
    {
        // Đường dẫn file merge cuối
        $mergedFolder = "{$parentFolder}/merged/" . date('d-m-Y');
        $absoluteMergedFolder = $this->getAbsolutePublicPath($mergedFolder);

        // File merge cuối cùng
        $finalFilePath = "{$absoluteMergedFolder}/{$fileName}";
        $finalFile = fopen($finalFilePath, 'wb');

        // Lấy đường dẫn folder chunks
        $absoluteChunkDir = $this->getAbsolutePublicPath($chunkDir);

        // Merge tuần tự
        for ($i = 0; $i < $totalChunks; $i++) {
            $path = "{$absoluteChunkDir}/{$i}";
            $chunk = fopen($path, 'rb');

            stream_copy_to_stream($chunk, $finalFile);

            fclose($chunk);
            $this->safeDeleteFile($path);
        }

        fclose($finalFile);

        // Xóa folder chunks sau khi merge
        @rmdir($absoluteChunkDir);

        return [
            'done' => true,
            'merged_path' => "{$mergedFolder}/{$fileName}",
            'message' => 'Tải lên thành công! Đang bắt đầu kiểm tra...'
        ];
    }

    /**
     * Xóa các file trong thư mục được tạo khác ngày hôm nay
     *
     * @param string $directory Đường dẫn thư mục cần cleanup
     * @return void
     */
    public function cleanupOldOverlapFiles(string $directory): void
    {
        try {
            if (!is_dir($directory)) {
                return;
            }

            // Lấy ngày hôm nay (Y-m-d format)
            $today = date('Y-m-d');

            // Quét tất cả files trong thư mục
            $files = scandir($directory);

            $deletedCount = 0;
            $deletedSize = 0;

            foreach ($files as $file) {
                // Skip . và ..
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $filePath = $directory . '/' . $file;

                // Chỉ xử lý files, không xử lý folders
                if (!is_file($filePath)) {
                    continue;
                }

                // Lấy thời gian tạo file (modification time)
                $fileModifiedTime = filemtime($filePath);
                $fileDate = date('Y-m-d', $fileModifiedTime);

                // Nếu file được tạo khác ngày hôm nay → Xóa
                if ($fileDate !== $today) {
                    $fileSize = filesize($filePath);

                    if ($this->safeDeleteFile($filePath)) {
                        $deletedCount++;
                        $deletedSize += $fileSize;
                        Log::info("Deleted old overlap file: $file (Date: $fileDate, Size: " . round($fileSize / 1024, 2) . ' KB)');
                    }
                }
            }

            if ($deletedCount > 0) {
                Log::info("Cleanup completed: Deleted $deletedCount old files, freed " . round($deletedSize / 1024 / 1024, 2) . ' MB');
            }
        } catch (Exception $e) {
            // Log error nhưng không throw exception để không ảnh hưởng main process
            Log::warning('Error cleaning up old overlap files: ' . $e->getMessage());
        }
    }
}
