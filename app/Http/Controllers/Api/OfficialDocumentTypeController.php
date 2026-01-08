<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfficialDocumentType\ListRequest;
use App\Http\Requests\OfficialDocumentType\StoreRequest;
use App\Http\Requests\OfficialDocumentType\UpdateRequest;
use App\Services\OfficialDocumentTypeService;

class OfficialDocumentTypeController extends Controller
{
    public function __construct()
    {
        $this->service = app(OfficialDocumentTypeService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $this->service->list($request->validated());
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
