<?php

namespace App\Services;

use App\Services\BaseService;
use Exception;
use Log;

class HandlerUploadFileService extends BaseService
{
    public function storeAndRemoveOld($file, string $rootFolder, string $folder = '', string $oldPath = null)
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

    /**
     * Xóa file an toàn với xử lý lỗi
     *
     * @param string $filePath Đường dẫn file relative từ public
     * @return bool
     */
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
        if (!is_dir($this->getAbsolutePublicPath($folder))) {
            return false;
        }

        try {
            $this->deleteFolderContents($folder);
            return rmdir($folder);
        } catch (\Exception $e) {
            \Log::error("Failed to remove folder: {$folder}. Error: " . $e->getMessage());
            return false;
        }
    }

    private function deleteFolderContents(string $folder)
    {
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

    public function uploadChunk(array $request, string $folder)
    {
        $file = $request['file'];

        $folder = $folder . '/' . auth()->id() . '/' . date('d-m-Y');
        $uploadsDir = $this->getAbsolutePublicPath($folder);

        try {
            $fileTmpLoc = $file->getPathname();
            $fileOriginalName = $file->getClientOriginalName();
            $fileName = pathinfo($fileOriginalName, PATHINFO_FILENAME);

            $id = $request['id'] ?? '';
            $fileLoc = "$uploadsDir/$fileOriginalName";

            if (strlen($id) != 13) {
                $id = uniqid();
                $file->move($uploadsDir, $fileOriginalName);
            } else {
                if (!file_exists($fileLoc))
                    throw new Exception('ID tệp không hợp lệ');

                file_put_contents($fileLoc, file_get_contents($fileTmpLoc), FILE_APPEND);
            }

            return [
                'id' => $id,
                'file_name' => $fileName,
                'path_zip' => $folder,
            ];
        } catch (Exception $e) {
            $this->removeFolder($uploadsDir);
            throw $e;
        }
    }
}
