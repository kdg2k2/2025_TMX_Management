<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainAndBusTicketDetail\ListRequest;
use App\Http\Requests\TrainAndBusTicketDetail\UpdateRequest;
use App\Services\TrainAndBusTicketDetailService;

class TrainAndBusTicketDetailController extends Controller
{
    public function __construct()
    {
        $this->service = app(TrainAndBusTicketDetailService::class);
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
