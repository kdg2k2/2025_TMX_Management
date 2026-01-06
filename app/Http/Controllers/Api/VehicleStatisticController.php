<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleStatistic\DataRequest;
use App\Services\VehicleStatisticService;

class VehicleStatisticController extends Controller
{
    public function __construct()
    {
        $this->service = app(VehicleStatisticService::class);
    }

    public function data(DataRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->data($request->validated()),
        ]));
    }
}
