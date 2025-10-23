<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BinddingSoftwareOwnership\DeleteBySoftwareOwnershipIdRequest;
use App\Http\Requests\BinddingSoftwareOwnership\DeleteRequest;
use App\Http\Requests\BinddingSoftwareOwnership\ListRequest;
use App\Http\Requests\BinddingSoftwareOwnership\StoreRequest;
use App\Services\BinddingSoftwareOwnershipService;

class BinddingSoftwareOwnershipController extends Controller
{
    public function __construct()
    {
        $this->service = app(BinddingSoftwareOwnershipService::class);
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
                'data' => $this->service->updateOrCreate($request->validated()),
                'message' => config('message.update'),
            ]);
        });
    }

    public function delete(DeleteRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->delete($request->validated()['id']),
                'message' => config('message.delete'),
            ]);
        });
    }

    public function deleteBySoftwareOwnershipId(DeleteBySoftwareOwnershipIdRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->deleteBySoftwareOwnershipId($request->validated()['id']),
                'message' => config('message.delete'),
            ]);
        });
    }
}
