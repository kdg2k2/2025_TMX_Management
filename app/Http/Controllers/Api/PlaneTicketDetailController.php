<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaneTicketDetail\ListRequest;
use App\Http\Requests\PlaneTicketDetail\UpdateRequest;
use App\Services\PlaneTicketDetailService;

class PlaneTicketDetailController extends Controller
{
    public function __construct()
    {
        $this->service = app(PlaneTicketDetailService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->list($request->validated()),
            ]);
        });
    }

    public function update(UpdateRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->update($request->validated()),
                'message' => config('message.update'),
            ]);
        });
    }
}
