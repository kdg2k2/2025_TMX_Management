<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractPayment\DeleteRequest;
use App\Services\ContractPaymentService;

class ContractPaymentController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractPaymentService::class);
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
