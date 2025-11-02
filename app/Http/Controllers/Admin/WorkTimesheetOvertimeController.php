<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WorkTimesheetOvertimeService;

class WorkTimesheetOvertimeController extends Controller
{
    public function __construct(){
        $this->service = app(WorkTimesheetOvertimeService::class);
    }
    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.work-timesheet.overtime.index', $this->service->baseOvertimeUpload());
        });
    }
}
