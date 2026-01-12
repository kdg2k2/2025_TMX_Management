<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\KasperskyCodeStatisticService;

class KasperskyCodeStatisticController extends Controller
{
    public function __construct()
    {
        $this->service = app(KasperskyCodeStatisticService::class);
    }

    public function index()
    {
        return $this->catchWeb(fn() => view('admin.pages.kaspersky.statistic.index'));
    }
}
