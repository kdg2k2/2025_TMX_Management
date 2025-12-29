<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceLoan\ApproveRequest;
use App\Http\Requests\DeviceLoan\RejectRequest;
use App\Services\DeviceLoanService;

class DeviceLoanController extends Controller
{
    public function __construct()
    {
        $this->service = app(DeviceLoanService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.device.loan.index', $this->service->getBaseDataForLCView());
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.device.loan.create', $this->service->getBaseDataForLCView(false));
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
