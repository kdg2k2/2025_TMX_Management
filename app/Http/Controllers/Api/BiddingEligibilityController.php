<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BiddingEligibilityService;
use App\Http\Requests\BiddingEligibility\ListRequest;
use App\Http\Requests\BiddingEligibility\StoreRequest;
use App\Http\Requests\BiddingEligibility\DeleteRequest;
use App\Http\Requests\BiddingEligibility\DeleteByEligibilityIdRequest;

class BiddingEligibilityController extends Controller
{
    public function __construct()
    {
        $this->service = app(BiddingEligibilityService::class);
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

    public function deleteByEligibilityIdRequest(DeleteByEligibilityIdRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->deleteByEligibilityIdRequest($request->validated()['id']),
                'message' => config('message.delete'),
            ]);
        });
    }
}
