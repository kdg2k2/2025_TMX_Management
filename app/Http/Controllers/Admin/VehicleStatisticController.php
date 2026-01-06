<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VehicleStatisticService;

class VehicleStatisticController extends Controller
{
    public function __construct()
    {
        $this->service = app(VehicleStatisticService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.vehicle.statistic.index');
        });
    }
}
