<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->service = app(AuthService::class);
    }

    public function getLogin()
    {
        return view('admin.pages.auth.login');
    }

    public function postLogin(LoginRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->login($request->validated()),
                'message' => 'Đăng nhập thành công',
            ], 200);
        });
    }

    public function logout()
    {
        return $this->catchAPI(function () {
            return response()->json([
                'data' => $this->service->logout(auth()),
                'message' => 'Đăng xuất thành công',
            ], 200);
        });
    }
}
