<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractScanFile\FindByIdRequest;
use App\Http\Requests\ContractScanFile\ListRequest;
use App\Http\Requests\ContractScanFile\StoreRequest;
use App\Http\Requests\ContractScanFile\UpdateRequest;
use App\Services\ContractScanFileService;

class ContractScanFileController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractScanFileService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->list($request->validated()),
            ]);
        });
    }

    public function viewFile(FindByIdRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->viewFile($request->validated()['id']),
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
