<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DossierUsageRegisterService;

class DossierUsageRegisterController extends Controller
{
    public function __construct()
    {
        $this->service = app(DossierUsageRegisterService::class);
    }

    public function index()
    {
        return $this->catchWeb(function ()  {
            return view('admin.pages.dossier.usage_register.index', $this->service->baseIndexData());
        });
    }
}
