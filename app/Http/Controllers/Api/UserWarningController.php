<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserWarning\StoreRequest;
use App\Services\UserWarningService;

class UserWarningController extends Controller
{
    public function __construct()
    {
        $this->service = app(UserWarningService::class);
    }

    public function store(StoreRequest $request)
    {
        return $this->tryThrow(function () use ($request) {
            return response()->json([
                'data' => $this->service->store($request->validated()),
                'message' => config('message.default'),
            ]);
        });
    }
}
