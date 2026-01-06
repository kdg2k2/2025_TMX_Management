<?php

namespace App\Http\Controllers\Api;

use App\Services\VehicleLoanService;
use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleLoan\ListRequest;
use App\Http\Requests\VehicleLoan\StoreRequest;
use App\Http\Requests\VehicleLoan\ReturnRequest;

class VehicleLoanController extends Controller
{
    public function __construct()
    {
        $this->service = app(VehicleLoanService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->list($request->validated()),
                'message' => config('message.list'),
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

    public function return(ReturnRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->return($request->validated()),
            'message' => config('message.default'),
        ]));
    }
}
