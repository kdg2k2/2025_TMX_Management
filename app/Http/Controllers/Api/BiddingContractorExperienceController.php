<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BiddingContractorExperience\DeleteRequest;
use App\Http\Requests\BiddingContractorExperience\ListRequest;
use App\Http\Requests\BiddingContractorExperience\StoreRequest;
use App\Services\BiddingContractorExperienceService;

class BiddingContractorExperienceController extends Controller
{
    public function __construct()
    {
        $this->service = app(BiddingContractorExperienceService::class);
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
}
