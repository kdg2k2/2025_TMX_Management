<?php

namespace App\Services;

use Log;
use Exception;
use Illuminate\Support\Str;
use App\Services\BaseService;

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

    public function getAbsolutePublicPath(string $filePath, bool $createDirIfNotExists = true)
    {
        // Kiểm tra nếu đã là đường dẫn tuyệt đối (Windows hoặc Linux)
        if (
            preg_match('/^[A-Z]:\\\\/i', $filePath)
        ) {
            $destinationPath = $filePath;
        } else {
            $destinationPath = public_path($filePath);
        }

        // Chỉ tạo thư mục nếu được yêu cầu VÀ path không có extension (là folder)
        if ($createDirIfNotExists && !str_contains(basename($destinationPath), '.')) {
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
        }

        return $destinationPath;
    }

    public function removePublicPath(string $path): string
    {
        $publicPath = rtrim(public_path(), DIRECTORY_SEPARATOR);

        // Chuẩn hoá separator cho Windows
        $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $normalizedPublic = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $publicPath);

        if (Str::startsWith($normalizedPath, $normalizedPublic)) {
            return ltrim(
                substr($normalizedPath, strlen($normalizedPublic)),
                DIRECTORY_SEPARATOR
            );
        }

        return $path;
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

            // Không tạo thư mục khi đang xóa file
            $fullPath = $this->getAbsolutePublicPath($filePath, false);

            // Kiểm tra file có tồn tại không
            if (!file_exists($fullPath)) {
                Log::info("File không tồn tại: {$fullPath}");
                return true;
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
                Log::error("Không thể xóa file: {$fullPath}. Error: " . (error_get_last()['message'] ?? 'Unknown error'));
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
        $file = $request['file'];
        $chunkIndex = (int) $request['dzchunkindex'];
        $totalChunks = (int) $request['dztotalchunkcount'];
        $fileName = $request['dzfilename'];

        // Tạo thư mục lưu các phần chunk
        $chunkDir = "{$parentFolder}/chunks/{$fileName}";
        $absoluteChunkDir = $this->getAbsolutePublicPath($chunkDir);

        // Lưu chunk vào đúng vị trí
        $chunkPath = "{$absoluteChunkDir}/{$chunkIndex}";
        $file->move($absoluteChunkDir, $chunkIndex);

        // QUAN TRỌNG: Đảm bảo file được ghi xong
        clearstatcache(true, $chunkPath);

        // Nếu chưa phải chunk cuối → kết thúc
        if ($chunkIndex < $totalChunks - 1) {
            return [
                'done' => false,
                'message' => "Chunk {$chunkIndex}/{$totalChunks} uploaded"
            ];
        }

        // Chunk cuối → kiểm tra tất cả chunks trước khi merge
        return $this->mergeChunks($parentFolder, $fileName, $totalChunks, $chunkDir);
    }

    private function mergeChunks(string $parentFolder, string $fileName, int $totalChunks, string $chunkDir)
    {
        $absoluteChunkDir = $this->getAbsolutePublicPath($chunkDir);

        // BƯỚC 1: Kiểm tra tất cả chunks có đủ không
        $missingChunks = [];
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = "{$absoluteChunkDir}/{$i}";
            if (!file_exists($chunkPath) || filesize($chunkPath) === 0) {
                $missingChunks[] = $i;
            }
        }

        if (!empty($missingChunks)) {
            \Log::error('Missing chunks: ' . implode(', ', $missingChunks));
            throw new Exception('Thiếu chunks: ' . implode(', ', $missingChunks));
        }

        // BƯỚC 2: Tạo folder merge
        $mergedFolder = "{$parentFolder}/merged/" . date('d-m-Y');
        $absoluteMergedFolder = $this->getAbsolutePublicPath($mergedFolder);

        // BƯỚC 3: Tạo file tạm với tên unique để tránh conflict
        $tempFileName = uniqid('temp_') . '_' . $fileName;
        $tempFilePath = "{$absoluteMergedFolder}/{$tempFileName}";
        $finalFilePath = "{$absoluteMergedFolder}/{$fileName}";

        try {
            // Mở file output với binary write mode
            $finalFile = fopen($tempFilePath, 'wb');

            if ($finalFile === false) {
                throw new Exception("Không thể tạo file merge: {$tempFilePath}");
            }

            // BƯỚC 4: Merge từng chunk
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = "{$absoluteChunkDir}/{$i}";

                // Kiểm tra lại chunk trước khi đọc
                if (!file_exists($chunkPath)) {
                    fclose($finalFile);
                    @unlink($tempFilePath);
                    throw new Exception("Chunk {$i} bị mất trong quá trình merge");
                }

                // Mở chunk với read binary mode và lock
                $chunk = fopen($chunkPath, 'rb');

                if ($chunk === false) {
                    fclose($finalFile);
                    @unlink($tempFilePath);
                    throw new Exception("Không thể đọc chunk {$i}");
                }

                // Lock file để tránh race condition
                if (!flock($chunk, LOCK_SH)) {  // Shared lock for reading
                    fclose($chunk);
                    fclose($finalFile);
                    @unlink($tempFilePath);
                    throw new Exception("Không thể lock chunk {$i}");
                }

                // Copy data từ chunk vào file merge
                $bytesCopied = stream_copy_to_stream($chunk, $finalFile);

                // Unlock và close chunk
                flock($chunk, LOCK_UN);
                fclose($chunk);

                // Log để debug
                \Log::info("Merged chunk {$i}: {$bytesCopied} bytes");
            }

            // BƯỚC 5: Flush và close file merge
            fflush($finalFile);
            fclose($finalFile);

            // BƯỚC 6: Đổi tên file tạm thành file chính thức
            if (file_exists($finalFilePath)) {
                @unlink($finalFilePath);
            }
            rename($tempFilePath, $finalFilePath);

            // BƯỚC 7: Verify file merge
            clearstatcache(true, $finalFilePath);
            $mergedFileSize = filesize($finalFilePath);

            \Log::info("Merge completed. Final file size: {$mergedFileSize} bytes");

            // BƯỚC 8: Xóa folder chunks sau khi merge thành công
            $this->removeFolder($absoluteChunkDir);

            return [
                'done' => true,
                'merged_path' => "{$mergedFolder}/{$fileName}",
                'file_size' => $mergedFileSize,
                'message' => 'Tải lên thành công! Đang bắt đầu kiểm tra...'
            ];
        } catch (Exception $e) {
            // Cleanup nếu có lỗi
            if (isset($finalFile) && is_resource($finalFile)) {
                fclose($finalFile);
            }
            if (file_exists($tempFilePath)) {
                @unlink($tempFilePath);
            }

            \Log::error('Merge error: ' . $e->getMessage());
            throw $e;
        }
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
