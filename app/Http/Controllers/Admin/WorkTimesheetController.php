<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WorkTimesheetService;

class WorkTimesheetController extends Controller
{
    public function __construct()
    {
        $this->service = app(WorkTimesheetService::class);
    }

    public function index(){
        return $this->catchWeb(function(){
            return view('admin.pages.work-timesheet.index', $this->service->baseIndexData());
        });
    }
}
