<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DeviceStatisticService;

class DeviceStatisticController extends Controller
{
       public function __construct()
    {
        $this->service = app(DeviceStatisticService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.device.statistic.index');
        });
    }
}
