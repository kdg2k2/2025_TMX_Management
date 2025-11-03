<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PayrollService;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->service = app(PayrollService::class);
    }
}
