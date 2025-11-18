<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DossierSyntheticService;

class DossierSyntheticController extends Controller
{
    public function __construct()
    {
        $this->service = app(DossierSyntheticService::class);
    }

    public function index()
    {
        return $this->catchWeb(function ()  {
            return view('admin.pages.dossier.synthetic.index', $this->service->baseIndexData());
        });
    }
}
