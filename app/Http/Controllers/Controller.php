<?php

namespace App\Http\Controllers;

use App\Traits\GuardTraits;
use App\Traits\TryCatchTraits;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, TryCatchTraits, GuardTraits;

    protected $service;
}
