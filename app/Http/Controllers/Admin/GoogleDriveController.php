<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GoogleDrive\CallbackRequest;
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
}
