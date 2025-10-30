<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserTimeTableService;

class UserTimeTableController extends Controller
{
    public function __construct()
    {
        $this->service = app(UserTimeTableService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.user.timetable', $this->service->getBaseListData());
        });
    }
}
