<?php

namespace App\Services;

use App\Services\BaseService;
use Exception;

class HandlerUploadFileService extends BaseService
{
    public function storeAndRemoveOld($file, string $rootFolder, string $folder, string $oldPath = null)
    {
        $folderSave = "uploads/$rootFolder/$folder";
        $destinationPath = $this->getAbsolutePublicPath($folderSave);

        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        // Nếu là file dạng UploadedFile (từ request upload)
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            $imageName = uniqid($folder) . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $imageName);
        }
        // Nếu là file dạng đường dẫn thực tế (Seeder)
        elseif (is_string($file) && file_exists($file)) {
            $imageName = uniqid($folder) . '.' . pathinfo($file, PATHINFO_EXTENSION);
            copy($file, $destinationPath . '/' . $imageName);
        } else {
            throw new Exception('Định dạng file không hợp lệ.');
        }

        // Xoá file cũ nếu có
        if ($oldPath && file_exists($this->getAbsolutePublicPath($oldPath))) {
            $this->removeFile($oldPath);
        }

        return "$folderSave/$imageName";
    }

    public function getAbsolutePublicPath(string $filePath)
    {
        if (\Illuminate\Support\Str::startsWith($filePath, public_path())) {
            return $filePath;
        } else {
            return public_path($filePath);
        }
    }

    public function removeFile(array|string $paths)
    {
        if (!is_array($paths))
            $paths = [$paths];

        foreach ($paths as $p) {
            if (file_exists($this->getAbsolutePublicPath($p))) {
                $this->safeDeleteFile($p);
            }
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
                $this->removeFile($fullPath);
            }
        }
    }

    public function uploadChunk(array $request, string $folder)
    {
        $file = $request['file'];

        $folder = $folder . '/' . auth()->id() . '/' . date('d-m-Y');
        $uploadsDir = $this->getAbsolutePublicPath($folder);
        if (!is_dir($uploadsDir))
            mkdir($uploadsDir, 0777, true);

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
