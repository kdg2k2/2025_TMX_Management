<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainAndBusTicket\ListRequest;
use App\Http\Requests\TrainAndBusTicket\StoreRequest;
use App\Http\Requests\TrainAndBusTicket\UpdateRequest;
use App\Services\TrainAndBusTicketService;

class TrainAndBusTicketController extends Controller
{
    public function __construct()
    {
        $this->service = app(TrainAndBusTicketService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->list($request->validated()),
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
}
