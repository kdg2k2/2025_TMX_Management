<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProfessionalRecordTypeService;
use App\Http\Requests\ProfessionalRecordType\ListRequest;
use App\Http\Requests\ProfessionalRecordType\StoreRequest;
use App\Http\Requests\ProfessionalRecordType\UpdateRequest;

class ProfessionalRecordTypeController extends Controller
{
    public function __construct()
    {
        $this->service = app(ProfessionalRecordTypeService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->list($request->validated()),
                'message' => config('message.list'),
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
