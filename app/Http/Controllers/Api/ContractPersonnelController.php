<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractPersonnel\ListRequest;
use App\Http\Requests\ContractPersonnel\SynctheticRequest;
use App\Services\ContractPersonnelService;

class ContractPersonnelController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractPersonnelService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->list($request->validated()),
        ]));
    }

    public function synthetic(SynctheticRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->synthetic($request->validated()),
        ]));
    }
}
