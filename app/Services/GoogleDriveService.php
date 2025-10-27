<?php

namespace App\Services;

use Google\Service\Drive;
use Google\Client;
use Illuminate\Http\UploadedFile;

class GoogleDriveService
{
    protected $client;
    public $service;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path(config('google-drive.storage.credentials')));
        $this->client->addScope(Drive::DRIVE);
        $this->client->setRedirectUri(config('app.url') . '/google/drive/callback');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');

        // Load token nếu đã có
        if (file_exists(storage_path(config('google-drive.storage.token')))) {
            $accessToken = json_decode(file_get_contents(storage_path(config('google-drive.storage.token'))), true);
            $this->client->setAccessToken($accessToken);

            // Refresh token nếu hết hạn
            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    file_put_contents(storage_path(config('google-drive.storage.token')), json_encode($this->client->getAccessToken()));
                }
            }
        }

        $this->service = new Drive($this->client);
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function authenticate($code)
    {
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
        file_put_contents(storage_path(config('google-drive.storage.token')), json_encode($accessToken));
        return $accessToken;
    }

    /**
     * Tạo folder đơn
     * @param string $folderName
     * @param string|null $parentId
     * @return array
     */
    public function createFolder(string $folderName, string $parentId = null)
    {
        $fileMetadata = new Drive\DriveFile([
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder'
        ]);

        if ($parentId) {
            $fileMetadata->setParents([$parentId]);
        }

        $folder = $this->service->files->create($fileMetadata, [
            'fields' => 'id, name, createdTime, modifiedTime'
        ]);

        return [
            'success' => true,
            'folder_id' => $folder->id,
            'folder_name' => $folder->name,
            'created_time' => $folder->createdTime,
            'modified_time' => $folder->modifiedTime
        ];
    }

    /**
     * Xóa folder
     * @param string $folderId
     * @return array
     */
    public function deleteFolder(string $folderId)
    {
        $this->service->files->delete($folderId);

        return [
            'success' => true,
            'message' => 'Folder deleted successfully'
        ];
    }

    /**
     * Upload file đơn
     * @param string $filePath - Đường dẫn file local
     * @param string $fileName - Tên file trên Drive
     * @param string|null $folderId - ID folder đích
     * @param string|null $mimeType - MIME type
     * @return array
     */
    public function uploadFile(string $filePath, string $fileName, string $folderId = null, string $mimeType = null)
    {
        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'message' => 'File không tồn tại: ' . $filePath
            ];
        }

        $fileMetadata = new Drive\DriveFile([
            'name' => $fileName,
            'parents' => $folderId ? [$folderId] : []
        ]);

        $content = file_get_contents($filePath);
        $mimeType = $mimeType ?? mime_content_type($filePath);

        $uploadedFile = $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
            'fields' => 'id, name, mimeType, size, webViewLink, createdTime'
        ]);

        return [
            'success' => true,
            'file_id' => $uploadedFile->id,
            'file_name' => $uploadedFile->name,
            'mime_type' => $uploadedFile->mimeType,
            'size' => $uploadedFile->size,
            'web_view_link' => $uploadedFile->webViewLink,
            'created_time' => $uploadedFile->createdTime
        ];
    }

    /**
     * Xóa file
     * @param string $fileId
     * @return array
     */
    public function deleteFile(string $fileId)
    {
        $this->service->files->delete($fileId);

        return [
            'success' => true,
            'message' => 'File deleted successfully'
        ];
    }

    /**
     * Tạo folder theo path (tái sử dụng createFolder)
     * @param string $path - VD: "A/01/11"
     * @param string|null $rootParentId
     * @return string - Trả về folder ID cuối cùng
     */
    public function createFolderByPath(string $path, string $rootParentId = null): string
    {
        $path = trim($path, '/');
        $folders = explode('/', $path);
        $currentParentId = $rootParentId;

        foreach ($folders as $folderName) {
            // Tìm folder hiện có
            $existingFolder = $this->findFolder($folderName, $currentParentId);

            if ($existingFolder) {
                $currentParentId = $existingFolder['id'];
            } else {
                // Tạo mới bằng hàm gốc
                $result = $this->createFolder($folderName, $currentParentId);
                $currentParentId = $result['folder_id'];
            }
        }

        return $currentParentId;
    }

    /**
     * Upload file theo path (tái sử dụng createFolderByPath + uploadFile)
     * @param string $localFilePath - Đường dẫn file local
     * @param string $driveFolderPath - Path folder trên Drive (VD: "A/01/11")
     * @param string|null $customFileName - Tên file tùy chỉnh
     * @return array
     */
    public function uploadFileByPath(string $localFilePath, string $driveFolderPath, string $customFileName = null)
    {
        // Tạo folder theo path
        $folderId = $this->createFolderByPath($driveFolderPath);

        // Upload file bằng hàm gốc
        $fileName = $customFileName ?? basename($localFilePath);
        return $this->uploadFile($localFilePath, $fileName, $folderId);
    }

    /**
     * Khởi tạo folders từ mảng (tái sử dụng createFolderByPath)
     * @param array $structure
     * @param string|null $rootParentId
     * @return array
     */
    public function initFolders(array $structure, string $rootParentId = null)
    {
        $paths = $this->arrayToFolderPaths($structure);
        $results = [];

        foreach ($paths as $path) {
            $folderId = $this->createFolderByPath($path, $rootParentId);
            $results[$path] = [
                'id' => $folderId,
                'path' => $path
            ];
        }

        return $results;
    }

    /**
     * Chuyển mảng thành danh sách paths
     */
    private function arrayToFolderPaths(array $structure, string $prefix = ''): array
    {
        $paths = [];

        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                // Key là tên folder, value là children
                $currentPath = $prefix ? $prefix . '/' . $key : $key;
                $paths[] = $currentPath;
                $paths = array_merge($paths, $this->arrayToFolderPaths($value, $currentPath));
            } else {
                // Value là tên folder
                $currentPath = $prefix ? $prefix . '/' . $value : $value;
                $paths[] = $currentPath;
            }
        }

        return $paths;
    }

    /**
     * Tìm folder theo tên trong parent
     */
    private function findFolder(string $folderName, string $parentId = null): ?array
    {
        $query = "mimeType='application/vnd.google-apps.folder' and name='{$folderName}' and trashed=false";

        if ($parentId) {
            $query .= " and '{$parentId}' in parents";
        } else {
            $query .= " and 'root' in parents";
        }

        $response = $this->service->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)',
            'pageSize' => 1
        ]);

        $files = $response->getFiles();

        if (count($files) > 0) {
            return [
                'id' => $files[0]->id,
                'name' => $files[0]->name
            ];
        }

        return null;
    }

    /**
     * Lấy danh sách folders
     */
    public function listFolders(string $parentId = null)
    {
        $query = "mimeType='application/vnd.google-apps.folder' and trashed=false";

        if ($parentId) {
            $query .= " and '{$parentId}' in parents";
        }

        $response = $this->service->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name, createdTime, modifiedTime)',
            'orderBy' => 'name'
        ]);

        return [
            'success' => true,
            'folders' => $response->getFiles()
        ];
    }

    /**
     * Lấy thông tin folder
     */
    public function getFolder(string $folderId)
    {
        $folder = $this->service->files->get($folderId, [
            'fields' => 'id, name, createdTime, modifiedTime, parents'
        ]);

        return [
            'success' => true,
            'folder' => $folder
        ];
    }

    /**
     * Cập nhật tên folder
     */
    public function updateFolder(string $folderId, string $newName)
    {
        $fileMetadata = new Drive\DriveFile([
            'name' => $newName
        ]);

        $folder = $this->service->files->update($folderId, $fileMetadata, [
            'fields' => 'id, name, modifiedTime'
        ]);

        return [
            'success' => true,
            'folder_id' => $folder->id,
            'folder_name' => $folder->name,
            'modified_time' => $folder->modifiedTime
        ];
    }

    /**
     * Di chuyển folder
     */
    public function moveFolder(string $folderId, string $newParentId)
    {
        $file = $this->service->files->get($folderId, ['fields' => 'parents']);
        $previousParents = implode(',', $file->getParents());

        $folder = $this->service->files->update($folderId, new Drive\DriveFile(), [
            'addParents' => $newParentId,
            'removeParents' => $previousParents,
            'fields' => 'id, parents'
        ]);

        return [
            'success' => true,
            'folder_id' => $folder->id,
            'parents' => $folder->parents
        ];
    }

    /**
     * Tìm kiếm folder
     */
    public function searchFolder(string $folderName)
    {
        $query = "mimeType='application/vnd.google-apps.folder' and name contains '{$folderName}' and trashed=false";

        $response = $this->service->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name, createdTime, modifiedTime)'
        ]);

        return [
            'success' => true,
            'folders' => $response->getFiles()
        ];
    }
}
