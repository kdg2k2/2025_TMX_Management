<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ContractIntermediateProductService;
use App\Http\Requests\ContractIntermediateProduct\ListRequest;
use App\Http\Requests\ContractIntermediateProduct\ExportRequest;
use App\Http\Requests\ContractIntermediateProduct\ImportRequest;

class ContractIntermediateProductController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractIntermediateProductService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(fn() => response()->json($this->service->list($request->validated())));
    }

    public function import(ImportRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->import($request->validated()),
            'message' => config('message.default')
        ]));
    }

    public function export(ExportRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->export($request->validated()),
        ]));
    }
}
