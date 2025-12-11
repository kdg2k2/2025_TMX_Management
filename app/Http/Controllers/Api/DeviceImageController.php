<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceImage\ListRequest;
use App\Http\Requests\DeviceImage\StoreRequest;
use App\Http\Requests\DeviceImage\UpdateRequest;
use App\Services\DeviceImageService;

class DeviceImageController extends Controller
{
    public function __construct()
    {
        $this->service = app(DeviceImageService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->list($request->validated()),
        ]));
    }

    public function store(StoreRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->store($request->validated()),
            'message' => config('message.store'),
        ]));
    }

    public function update(UpdateRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->update($request->validated()),
            'message' => config('message.update'),
        ]));
    }
}
