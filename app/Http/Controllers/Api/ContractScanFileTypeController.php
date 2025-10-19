<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ContractScanFileTypeService;
use App\Http\Requests\ContractScanFileType\ListRequest;
use App\Http\Requests\ContractScanFileType\StoreRequest;
use App\Http\Requests\ContractScanFileType\UpdateRequest;

class ContractScanFileTypeController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractScanFileTypeService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->list($request->validated()),
            ]);
        });
    }

    public function store(StoreRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->store($request->validated()),
                'message' => config('message.store'),
            ]);
        });
    }

    public function update(UpdateRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->update($request->validated()),
                'message' => config('message.update'),
            ]);
        });
    }
}
