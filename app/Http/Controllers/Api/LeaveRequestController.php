<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveRequest\AdjustApprovalRequest;
use App\Http\Requests\LeaveRequest\AdjustRequest;
use App\Http\Requests\LeaveRequest\ApprovalRequest;
use App\Http\Requests\LeaveRequest\GetTotalLeaveDaysRequest;
use App\Http\Requests\LeaveRequest\ListRequest;
use App\Http\Requests\LeaveRequest\StoreRequest;
use App\Services\LeaveRequestService;

class LeaveRequestController extends Controller
{
    public function __construct()
    {
        $this->service = app(LeaveRequestService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->list($request->validated()),
            ]);
        });
    }

    public function getTotalLeaveDays(GetTotalLeaveDaysRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $validated = $request->validated();
            return response()->json([
                'data' => $this->service->getTotalLeaveDays(
                    $validated['from_date'],
                    $validated['to_date'],
                    $validated['type'] ?? 'one_day'
                ),
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

    public function adjust(AdjustRequest $request)
    {
        return $this->tryThrow(function () use ($request) {
            return response()->json([
                'data' => $this->service->adjustRequest($request->validated()),
                'message' => config('message.request_approve'),
            ]);
        });
    }

    public function adjustApprove(AdjustApprovalRequest $request)
    {
        return $this->tryThrow(function () use ($request) {
            return response()->json([
                'data' => $this->service->adjustApprovalRequest($request->validated()),
                'message' => config('message.accept'),
            ]);
        });
    }

    public function adjustReject(AdjustApprovalRequest $request)
    {
        return $this->tryThrow(function () use ($request) {
            return response()->json([
                'data' => $this->service->adjustApprovalRequest($request->validated()),
                'message' => config('message.reject'),
            ]);
        });
    }
}
