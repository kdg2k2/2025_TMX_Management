<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BiddingImplementationPersonnel\DeleteRequest;
use App\Http\Requests\BiddingImplementationPersonnel\ListRequest;
use App\Http\Requests\BiddingImplementationPersonnel\StoreRequest;
use App\Services\BiddingImplementationPersonnelService;

class BiddingImplementationPersonnelController extends Controller
{
    public function __construct()
    {
        $this->service = app(BiddingImplementationPersonnelService::class);
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

}
