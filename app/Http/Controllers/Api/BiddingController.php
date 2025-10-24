<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bidding\DownloadBuiltResultRequest;
use App\Http\Requests\Bidding\ListRequest;
use App\Http\Requests\Bidding\StoreRequest;
use App\Http\Requests\Bidding\UpdateRequest;
use App\Services\BiddingService;

class BiddingController extends Controller
{
    public function __construct()
    {
        $this->service = app(BiddingService::class);
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

    public function downloadBuiltResult(DownloadBuiltResultRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->downloadBuiltResult($request->validated()['id']),
                'message' => config('message.render_file'),
            ]);
        });
    }
}
