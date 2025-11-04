<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskSchedule\FindByIdRequest;
use App\Services\TaskScheduleService;

class TaskScheduleController extends Controller
{
    public function __construct()
    {
        $this->service = app(TaskScheduleService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.task-schedule.index');
        });
    }

    public function edit(FindByIdRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.task-schedule.edit', $this->service->getBaseUpdateView($request['id']));
        });
    }
}
