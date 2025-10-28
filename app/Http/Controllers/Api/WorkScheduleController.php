<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkSchedule\ApprovalRequest;
use App\Http\Requests\WorkSchedule\ListRequest;
use App\Http\Requests\WorkSchedule\ReturnApprovalRequest;
use App\Http\Requests\WorkSchedule\ReturnRequest;
use App\Http\Requests\WorkSchedule\StoreRequest;
use App\Services\WorkScheduleService;

class WorkScheduleController extends Controller
{
    public function __construct()
    {
        $this->service = app(WorkScheduleService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->list($request->validated()),
            ]);
        });
    }

    public function store(StoreRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->store($request->validated()),
                'message' => config('message.request_approve'),
            ]);
        });
    }

    public function approve(ApprovalRequest $request)
    {
        return $this->tryThrow(function () use ($request) {
            return response()->json([
                'data' => $this->service->approvalRequest($request->validated()),
                'message' => config('message.accept'),
            ]);
        });
    }

    public function reject(ApprovalRequest $request)
    {
        return $this->tryThrow(function () use ($request) {
            return response()->json([
                'data' => $this->service->approvalRequest($request->validated()),
                'message' => config('message.reject'),
            ]);
        });
    }

    public function return(ReturnRequest $request)
    {
        return $this->tryThrow(function () use ($request) {
            return response()->json([
                'data' => $this->service->returnRequest($request->validated()),
                'message' => config('message.request_approve'),
            ]);
        });
    }

    public function returnApprove(ReturnApprovalRequest $request)
    {
        return $this->tryThrow(function () use ($request) {
            return response()->json([
                'data' => $this->service->returnApprovalRequest($request->validated()),
                'message' => config('message.accept'),
            ]);
        });
    }

    public function returnReject(ReturnApprovalRequest $request)
    {
        return $this->tryThrow(function () use ($request) {
            return response()->json([
                'data' => $this->service->returnApprovalRequest($request->validated()),
                'message' => config('message.reject'),
            ]);
        });
    }
}
