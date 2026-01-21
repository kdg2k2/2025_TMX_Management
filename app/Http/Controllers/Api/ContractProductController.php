<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractProduct\GetContractYearsRequest;
use App\Http\Requests\ContractProduct\ListRequest;
use App\Services\ContractProductService;

class ContractProductController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractProductService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->list($request->validated()),
        ]));
    }

    public function getContractYears(GetContractYearsRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->getContractYears($request->validated()['contract_id'])
        ]));
    }
}
