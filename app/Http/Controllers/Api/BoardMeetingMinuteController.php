<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BoardMeetingMinute\ListRequest;
use App\Http\Requests\BoardMeetingMinute\StoreRequest;
use App\Http\Requests\BoardMeetingMinute\UpdateRequest;
use App\Services\BoardMeetingMinuteService;

class BoardMeetingMinuteController extends Controller
{
    public function __construct()
    {
        $this->service = app(BoardMeetingMinuteService::class);
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
