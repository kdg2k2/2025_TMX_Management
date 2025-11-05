<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DossierPlanService;

class DossierPlanController extends Controller
{
    public function __construct()
    {
        $this->service = app(DossierPlanService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.dossier.plan.index', $this->service->baseIndexData());
        });
    }
}
