<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaneTicket\ListRequest;
use App\Http\Requests\PlaneTicket\StoreRequest;
use App\Services\PlaneTicketService;

class PlaneTicketController extends Controller
{
    public function __construct()
    {
        $this->service = app(PlaneTicketService::class);
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
                'message' => config('message.request_approve'),
            ]);
        });
    }
}
