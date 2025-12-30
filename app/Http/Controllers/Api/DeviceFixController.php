<?php

namespace App\Http\Controllers\Api;

use App\Services\DeviceFixService;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceFix\ListRequest;
use App\Http\Requests\DeviceFix\FixedRequest;
use App\Http\Requests\DeviceFix\StoreRequest;

class DeviceFixController extends Controller
{
    public function __construct()
    {
        $this->service = app(DeviceFixService::class);
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

    public function fixed(FixedRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->fixed($request->validated()),
            'message' => config('message.default'),
        ]));
    }
}
