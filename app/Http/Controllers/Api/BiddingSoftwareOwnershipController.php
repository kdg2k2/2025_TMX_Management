<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BiddingSoftwareOwnership\DeleteBySoftwareOwnershipIdRequest;
use App\Http\Requests\BiddingSoftwareOwnership\DeleteRequest;
use App\Http\Requests\BiddingSoftwareOwnership\ListRequest;
use App\Http\Requests\BiddingSoftwareOwnership\StoreRequest;
use App\Services\BiddingSoftwareOwnershipService;

class BiddingSoftwareOwnershipController extends Controller
{
    public function __construct()
    {
        $this->service = app(BiddingSoftwareOwnershipService::class);
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
