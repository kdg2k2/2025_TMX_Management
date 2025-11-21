<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProfessionalRecordPlanService;

class ProfessionalRecordPlanController extends Controller
{
    public function __construct()
    {
        $this->service = app(ProfessionalRecordPlanService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.professional-record.plan.index', $this->service->baseIndexData());
        });
    }
}
