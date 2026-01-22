<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractDisbursement\ListRequest;
use App\Services\ContractDisbursementService;

class ContractDisbursementController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractDisbursementService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->list($request->validated()),
        ]));
    }
}
