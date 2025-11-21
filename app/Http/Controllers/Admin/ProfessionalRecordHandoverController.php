<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProfessionalRecordHandoverService;

class ProfessionalRecordHandoverController extends Controller
{
    public function __construct()
    {
        $this->service = app(ProfessionalRecordHandoverService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.professional-record.handover.index', $this->service->baseIndexData());
        });
    }
}
