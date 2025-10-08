<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait HandlePublicFileTraits
{
    /**
     * Xóa file an toàn với xử lý lỗi
     * 
     * @param string $filePath Đường dẫn file relative từ public
     * @return bool
     */
    public function safeDeleteFile($filePath)
    {
        try {
            $fullPath = app(\App\Services\HandlerUploadFileService::class)->getAbsolutePublicPath($filePath);

            // Kiểm tra file có tồn tại không
            if (!file_exists($fullPath)) {
                Log::info("File không tồn tại: {$fullPath}");
                return true; // Coi như đã xóa thành công
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
}
