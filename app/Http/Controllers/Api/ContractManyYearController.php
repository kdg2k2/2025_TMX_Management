<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractManyYear\ListRequest;
use App\Services\ContractManyYearService;

class ContractManyYearController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractManyYearService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->list($request->validated()),
        ]));
    }
}
