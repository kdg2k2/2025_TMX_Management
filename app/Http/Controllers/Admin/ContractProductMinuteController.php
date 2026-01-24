<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractProductMinute\ApproveRequest;
use App\Http\Requests\ContractProductMinute\RejectRequest;
use App\Services\ContractProductMinuteService;

class ContractProductMinuteController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractProductMinuteService::class);
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
