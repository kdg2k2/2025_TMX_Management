<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractProductInspection\CancelRequest;
use App\Http\Requests\ContractProductInspection\InspectionRequest;
use App\Http\Requests\ContractProductInspection\ResponseRequest;
use App\Services\ContractProductInspectionService;

class ContractProductInspectionController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractProductInspectionService::class);
    }

    public function request(InspectionRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->store($request->validated()),
            'message' => config('message.request'),
        ]));
    }

    public function cancel(CancelRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            // 'data'=>$this->service->cancel($request->validated()),
            'message' => config('message.default'),
        ]));
    }

    public function response(ResponseRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            // 'data'=>$this->service->response($request->validated()),
            'message' => config('message.default'),
        ]));
    }
}
