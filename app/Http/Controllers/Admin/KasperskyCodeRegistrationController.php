<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\KasperskyCodeRegistrationService;
use App\Http\Requests\KasperskyCodeRegistration\RejectRequest;
use App\Http\Requests\KasperskyCodeRegistration\ApproveRequest;

class KasperskyCodeRegistrationController extends Controller
{
    public function __construct()
    {
        $this->service = app(KasperskyCodeRegistrationService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.kaspersky.registration.index', $this->service->getBaseDataForLCView());
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.kaspersky.registration.create', $this->service->getBaseDataForLCView());
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
