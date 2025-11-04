<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmploymentContractPersonnel\ListRequest;
use App\Http\Requests\EmploymentContractPersonnel\StoreRequest;
use App\Http\Requests\EmploymentContractPersonnel\SynctheticExcelRequest;
use App\Http\Requests\EmploymentContractPersonnel\UpdateRequest;
use App\Services\EmploymentContractPersonnelService;

class EmploymentContractPersonnelController extends Controller
{
    public function __construct()
    {
        $this->service = app(EmploymentContractPersonnelService::class);
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

    public function synctheticExcel(SynctheticExcelRequest $request){
        return $this->catchAPI(function()use($request){
            return response()->json([
                'data'=>$this->service->synctheticExcel($request->validated()),
            ]);
        });
    }
}
