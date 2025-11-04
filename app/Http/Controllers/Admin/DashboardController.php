<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return $this->catchWeb(fn() => view('admin.pages.dashboard.index'));
    }
}
