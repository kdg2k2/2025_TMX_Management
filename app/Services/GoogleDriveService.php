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
        $this->client->setAuthConfig(storage_path('app/google/credentials.json'));
        $this->client->addScope(Drive::DRIVE);
        $this->client->setRedirectUri(route('google.drive.callback'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');

        // Load token nếu đã có
        if (file_exists(storage_path('app/google/token.json'))) {
            $accessToken = json_decode(file_get_contents(storage_path('app/google/token.json')), true);
            $this->client->setAccessToken($accessToken);

            // Refresh token nếu hết hạn
            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    file_put_contents(storage_path('app/google/token.json'), json_encode($this->client->getAccessToken()));
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
        file_put_contents(storage_path('app/google/token.json'), json_encode($accessToken));
        return $accessToken;
    }

    public function initFolders(array $folders = [])
    {
        foreach ($folders as $item) {
            $this->createFolderByPath($item);
        }
    }

    /**
     * Tạo folder theo path, tự động tạo parent nếu chưa có
     * @param string $path - Ví dụ: "Folder A/Folder B/Folder C"
     * @param string|null $rootParentId - ID folder gốc (null = root Drive)
     * @return array
     */
    public function createFolderByPath(string $path, string $rootParentId = null)
    {
        // Loại bỏ dấu / ở đầu và cuối
        $path = trim($path, '/');

        // Tách path thành mảng folders
        $folders = explode('/', $path);

        $currentParentId = $rootParentId;
        $createdFolders = [];

        foreach ($folders as $folderName) {
            // Tìm folder trong parent hiện tại
            $existingFolder = $this->findFolderInParent($folderName, $currentParentId);

            if ($existingFolder) {
                // Folder đã tồn tại
                $currentParentId = $existingFolder['id'];
                $createdFolders[] = [
                    'name' => $folderName,
                    'id' => $existingFolder['id'],
                    'status' => 'existing'
                ];
            } else {
                // Tạo folder mới
                $newFolder = $this->createFolder($folderName, $currentParentId);
                $currentParentId = $newFolder['folder_id'];
                $createdFolders[] = [
                    'name' => $folderName,
                    'id' => $newFolder['folder_id'],
                    'status' => 'created'
                ];
            }
        }

        return [
            'success' => true,
            'path' => $path,
            'final_folder_id' => $currentParentId,
            'folders' => $createdFolders
        ];
    }

    /**
     * Tìm folder chính xác trong parent (cải tiến từ searchFolder)
     * @param string $folderName
     * @param string|null $parentId
     * @return array|null
     */
    private function findFolderInParent(string $folderName, string $parentId = null)
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
     * Lấy hoặc tạo folder theo path
     * @param string $path
     * @param string|null $rootParentId
     * @return string - Trả về folder ID cuối cùng
     */
    public function getOrCreateFolderByPath(string $path, string $rootParentId = null): string
    {
        $result = $this->createFolderByPath($path, $rootParentId);
        return $result['final_folder_id'];
    }

    /**
     * Tạo folder mới
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
     * Upload file
     */
    public function uploadFile(UploadedFile $file, string $folderId = null)
    {
        $fileMetadata = new Drive\DriveFile([
            'name' => $file->getClientOriginalName(),
            'parents' => $folderId ? [$folderId] : []
        ]);

        $content = file_get_contents($file->getRealPath());

        $uploadedFile = $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $file->getMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id, name, mimeType, size, webViewLink'
        ]);

        return [
            'success' => true,
            'file_id' => $uploadedFile->id,
            'file_name' => $uploadedFile->name,
            'mime_type' => $uploadedFile->mimeType,
            'size' => $uploadedFile->size,
            'web_view_link' => $uploadedFile->webViewLink
        ];
    }

    /**
     * Lấy danh sách folder
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
     * Xóa folder
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
     * Tìm kiếm folder theo tên
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
