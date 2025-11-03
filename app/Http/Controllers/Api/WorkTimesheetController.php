<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkTimesheet\ImportRequest;
use App\Http\Requests\WorkTimesheet\ListRequest;
use App\Http\Requests\WorkTimesheet\UpdateRequest;
use App\Services\WorkTimesheetService;

class WorkTimesheetController extends Controller
{
    public function __construct()
    {
        $this->service = app(WorkTimesheetService::class);
    }

    public function data(ListRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->data($request->validated()),
            ]);
        });
    }

    public function import(ImportRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->import($request->validated()),
                'message' => config('message.update'),
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
