<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BiddingProofContract\DeleteByProofContractIdRequest;
use App\Http\Requests\BiddingProofContract\DeleteRequest;
use App\Http\Requests\BiddingProofContract\ListRequest;
use App\Http\Requests\BiddingProofContract\StoreRequest;
use App\Services\BiddingProofContractService;

class BiddingProofContractController extends Controller
{
    public function __construct()
    {
        $this->service = app(BiddingProofContractService::class);
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

    public function deleteByProofContractId(DeleteByProofContractIdRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->deleteByProofContractId($request->validated()['id']),
                'message' => config('message.delete'),
            ]);
        });
    }

}
