<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractMainProduct\ExportRequest;
use App\Http\Requests\ContractMainProduct\ImportRequest;
use App\Http\Requests\ContractMainProduct\ListRequest;
use App\Services\ContractMainProductService;

class ContractMainProductController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractMainProductService::class);
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
