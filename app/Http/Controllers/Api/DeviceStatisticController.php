<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceStatistic\DataRequest;
use App\Services\DeviceStatisticService;

class DeviceStatisticController extends Controller
{
    public function __construct()
    {
        $this->service = app(DeviceStatisticService::class);
    }

    public function data(DataRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->data($request->validated()),
        ]));
    }
}
