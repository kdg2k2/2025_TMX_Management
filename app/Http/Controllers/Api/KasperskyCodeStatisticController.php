<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KasperskyCodeStatistic\DataRequest;
use App\Services\KasperskyCodeStatisticService;

class KasperskyCodeStatisticController extends Controller
{
    public function __construct()
    {
        $this->service = app(KasperskyCodeStatisticService::class);
    }

    public function data(DataRequest $request)
    {
        return $this->catchAPI(fn() => [
            'data' => $this->service->statistic($request->validated())
        ]);
    }
}
