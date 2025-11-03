<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\WorkTimesheetOvertimeDetail\ListRequest;
use App\Http\Controllers\Controller;
use App\Services\WorkTimesheetOvertimeDetailService;

class WorkTimesheetOvertimeDetailController extends Controller
{
    public function __construct()
    {
        $this->service = app(WorkTimesheetOvertimeDetailService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->list($request->validated()),
            ]);
        });
    }
}
