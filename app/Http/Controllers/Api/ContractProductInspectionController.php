<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractProductInspection\InspectionRequest;
use App\Http\Requests\ContractProductInspection\ResponseRequest;
use App\Services\ContractProductInspectionService;

class ContractProductInspectionController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractProductInspectionService::class);
    }

    public function inspection(InspectionRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            // 'data'=>$this->service->inspection($request->validated()),
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
