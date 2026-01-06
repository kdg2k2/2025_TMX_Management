<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleLoan\ApproveRequest;
use App\Http\Requests\VehicleLoan\RejectRequest;
use App\Services\VehicleLoanService;

class VehicleLoanController extends Controller
{
    public function __construct()
    {
        $this->service = app(VehicleLoanService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.vehicle.loan.index', $this->service->getBaseDataForLCView());
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.vehicle.loan.create', $this->service->getBaseDataForLCView(false));
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
