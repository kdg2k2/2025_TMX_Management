<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForceLogoutUserRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshRequest;
use App\Services\AuthService;
use App\Services\UserService;

class AuthController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {
        $this->service = app(AuthService::class);
    }

    public function login(LoginRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->login($request->validated()),
                'message' => 'Đăng nhập thành công',
            ], 200);
        });
    }

    public function refresh(RefreshRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->refresh($request->validated()),
                'message' => 'Làm mới thành công',
            ], 200);
        });
    }

    public function logout()
    {
        return $this->catchAPI(function () {
            return response()->json([
                'data' => $this->service->logout(),
                'message' => 'Đăng xuất thành công',
            ], 200);
        });
    }

    public function forceLogoutUser(ForceLogoutUserRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $userId = $request->validated()['user_id'];
            $this->userService->incrementJwtVersion($userId);
            return response()->json([
                'message' => "Bắt buộc đăng xuất userId = $userId thành công",
            ]);
        });
    }
}
