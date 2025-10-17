<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractAdvancePayment\StoreRequest;
use App\Http\Requests\ContractAdvancePayment\UpdateRequest;
use App\Services\ContractAdvancePaymentService;

class ContractAdvancePaymentController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractAdvancePaymentService::class);
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
