<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateRequest;
use App\Services\UserService;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->service = app(UserService::class);
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function update(UpdateRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->updateProfile($request->validated()),
            'message' => config('message.default'),
        ]));
    }
}
