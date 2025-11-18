<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DossierHandoverService;

class DossierHandoverController extends Controller
{
    public function __construct()
    {
        $this->service = app(DossierHandoverService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.dossier.handover.index', $this->service->baseIndexData());
        });
    }
}
