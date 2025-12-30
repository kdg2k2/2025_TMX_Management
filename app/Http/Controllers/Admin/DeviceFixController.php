<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceFix\ApproveRequest;
use App\Http\Requests\DeviceFix\RejectRequest;
use App\Services\DeviceFixService;

class DeviceFixController extends Controller
{
    public function __construct()
    {
        $this->service = app(DeviceFixService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.device.fix.index', $this->service->getBaseDataForLCView());
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.device.fix.create', $this->service->getBaseDataForLCView(false));
        });
    }

    public function approve(ApproveRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->approve($request->validated()),
            'message' => config('message.approve'),
        ]));
    }

    public function reject(RejectRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->reject($request->validated()),
            'message' => config('message.reject'),
        ]));
    }
}
