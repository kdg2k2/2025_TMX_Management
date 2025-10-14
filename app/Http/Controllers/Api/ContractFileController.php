<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractFile\FindByIdRequest;
use App\Http\Requests\ContractFile\ListRequest;
use App\Http\Requests\ContractFile\StoreRequest;
use App\Services\ContractFileService;

class ContractFileController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractFileService::class);
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
}
