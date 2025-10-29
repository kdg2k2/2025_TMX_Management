<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LeaveRequestService;

class LeaveRequestController extends Controller
{
    public function __construct()
    {
        $this->service = app(LeaveRequestService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.leave-request.index', $this->service->baseDataList());
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.leave-request.create', $this->service->baseDataCreateAndAdjust());
        });
    }
}
