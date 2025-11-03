<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkTimesheetOvertime\TemplateRequest;
use App\Http\Requests\WorkTimesheetOvertime\UploadRequest;
use App\Services\WorkTimesheetOvertimeService;

class WorkTimesheetOvertimeController extends Controller
{
    public function __construct()
    {
        $this->service = app(WorkTimesheetOvertimeService::class);
    }

    public function template(TemplateRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->template($request->validated()),
            ]);
        });
    }

    public function upload(UploadRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->upload($request->validated()),
                'message' => config('message.update'),
            ]);
        });
    }
}
