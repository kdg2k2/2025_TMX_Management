<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkTimesheet\UpdateRequest;
use App\Services\PayrollService;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->service = app(PayrollService::class);
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
