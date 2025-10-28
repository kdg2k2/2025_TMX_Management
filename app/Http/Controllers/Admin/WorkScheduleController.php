<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WorkScheduleService;

class WorkScheduleController extends Controller
{
    public function __construct()
    {
        $this->service = app(WorkScheduleService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.work-schedule.index', $this->service->baseDataList());
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.work-schedule.create', $this->service->baseDataCreate());
        });
    }
}
