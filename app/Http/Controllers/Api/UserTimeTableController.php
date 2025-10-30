<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserTimeTable\GetWeeksRequest;
use App\Http\Requests\UserTimeTable\ListRequest;
use App\Services\UserTimeTableService;

class UserTimeTableController extends Controller
{
    public function __construct()
    {
        $this->service = app(UserTimeTableService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->list($request->validated()),
            ]);
        });
    }

    public function getWeeks(GetWeeksRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->getWeeks($request->validated()['year']),
            ]);
        });
    }
}
