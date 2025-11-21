<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProfessionalRecordSyntheticService;

class ProfessionalRecordSyntheticController extends Controller
{
    public function __construct()
    {
        $this->service = app(ProfessionalRecordSyntheticService::class);
    }

    public function index()
    {
        return $this->catchWeb(function ()  {
            return view('admin.pages.professional-record.synthetic.index', $this->service->baseIndexData());
        });
    }
}
