<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskSchedule\ListRequest;
use App\Http\Requests\TaskSchedule\RunRequest;
use App\Http\Requests\TaskSchedule\UpdateRequest;
use App\Services\TaskScheduleService;

class TaskScheduleController extends Controller
{
    public function __construct()
    {
        $this->service = app(TaskScheduleService::class);
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

    public function run(RunRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->run($this->service->findById($request->validated()['id'])['code']),
                'message' => config('message.default'),
            ]);
        });
    }
}
