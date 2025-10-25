<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GoogleDrive\CallbackRequest;
use App\Http\Requests\GoogleDrive\CreateFolderRequest;
use App\Http\Requests\GoogleDrive\DeleteFolderRequest;
use App\Http\Requests\GoogleDrive\GetFolderRequest;
use App\Http\Requests\GoogleDrive\ListFoldersRequest;
use App\Http\Requests\GoogleDrive\MoveFolderRequest;
use App\Http\Requests\GoogleDrive\SearchFolderRequest;
use App\Http\Requests\GoogleDrive\UpdateFolderRequest;
use App\Http\Requests\GoogleDrive\UploadFileRequest;
use App\Services\GoogleDriveService;

class GoogleDriveController extends Controller
{
    public function __construct()
    {
        $this->service = app(GoogleDriveService::class);
    }

    public function auth()
    {
        return redirect($this->service->getAuthUrl());
    }

    public function callback(CallbackRequest $request)
    {
        try {
            $this->service->authenticate($request->code);
            return redirect(route('dashboard'))->with('success', 'Đã kết nối Google Drive thành công!');
        } catch (\Exception $e) {
            return redirect(route('google.drive.auth'))->with('error', 'Mã xác thực không hợp lệ. Vui lòng thử lại!');
        }
    }

    public function createFolder(CreateFolderRequest $request)
    {
        $result = $this->service->createFolder(
            $request->folder_name,
            $request->parent_id
        );

        return response()->json($result);
    }

    public function uploadFile(UploadFileRequest $request)
    {
        $result = $this->service->uploadFile(
            $request->file('file'),
            $request->folder_id
        );

        return response()->json($result);
    }

    public function listFolders(ListFoldersRequest $request)
    {
        $result = $this->service->listFolders($request->parent_id);
        return response()->json($result);
    }

    public function getFolder(GetFolderRequest $request)
    {
        $result = $this->service->getFolder($request->folder_id);
        return response()->json($result);
    }

    public function updateFolder(UpdateFolderRequest $request)
    {
        $result = $this->service->updateFolder(
            $request->folder_id,
            $request->new_name
        );

        return response()->json($result);
    }

    public function moveFolder(MoveFolderRequest $request)
    {
        $result = $this->service->moveFolder(
            $request->folder_id,
            $request->new_parent_id
        );

        return response()->json($result);
    }

    public function deleteFolder(DeleteFolderRequest $request)
    {
        $result = $this->service->deleteFolder($request->folder_id);
        return response()->json($result);
    }

    public function searchFolder(SearchFolderRequest $request)
    {
        $result = $this->service->searchFolder($request->folder_name);
        return response()->json($result);
    }
}
