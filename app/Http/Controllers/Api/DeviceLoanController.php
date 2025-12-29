<?php

namespace App\Http\Controllers\Api;

use App\Services\DeviceLoanService;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceLoan\ListRequest;
use App\Http\Requests\DeviceLoan\StoreRequest;
use App\Http\Requests\DeviceLoan\ReturnRequest;

class DeviceLoanController extends Controller
{
    public function __construct()
    {
        $this->service = app(DeviceLoanService::class);
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
                'message' => config('message.store'),
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
