<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->service = app(UserService::class);
    }

    /**
     * Hiển thị trang thông tin cá nhân
     */
    public function index()
    {
        return view('admin.pages.profile.index', [
            'user' => $this->service->baseDataForLCEView($this->getUserId(), true)['data'],
        ]);
    }
}
