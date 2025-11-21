<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProfessionalRecordUsageRegisterService;

class ProfessionalRecordUsageRegisterController extends Controller
{
    public function __construct()
    {
        $this->service = app(ProfessionalRecordUsageRegisterService::class);
    }

    public function index()
    {
        return $this->catchWeb(function ()  {
            return view('admin.pages.professional-record.usage_register.index', $this->service->baseIndexData());
        });
    }
}
