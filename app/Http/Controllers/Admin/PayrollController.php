<?php

namespace App\Http\Controllers\Admin;

use App\Services\PayrollService;
use App\Http\Controllers\Controller;

class PayrollController extends Controller
{
        public function __construct(){
        $this->service = app(PayrollService::class);
    }
    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.work-timesheet.payroll.index', $this->service->baseIndexData());
        });
    }
}
