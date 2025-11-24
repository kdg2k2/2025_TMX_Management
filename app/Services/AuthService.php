<?php

namespace App\Services;

use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;
use Exception;

class AuthService extends BaseService
{
    public function __construct(
        private UserService $userService
    ) {}

    public function login(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $isWebLogin = $request['web_login'];
            unset($request['web_login']);

            $loginCredentials = [
                'email' => $request['email'],
                'password' => $request['password'],
            ];

            $token = $isWebLogin
                ? auth()->attempt($loginCredentials, $request['remember'] ?? false)
                : auth('api')->attempt($loginCredentials);

            if (!$token)
                throw new Exception('Mật khẩu không chính xác', 401);

            $token = $isWebLogin
                ? (function () {
                    $route = route('dashboard');
                    $previousUrl = session('url.previous');
                    if ($previousUrl) {
                        session()->forget('url.previous');
                        $route = $previousUrl;
                    }
                    return $route;
                })()
                : $this->createNewToken($token, $this->createRefreshToken());

            // if ($this->checkVerifyAccountYet())
            //     throw new Exception('Tài khoản chưa xác thực', 403);

            return $token;
        });
    }

    private function checkVerifyAccountYet()
    {
        $guard = $this->getGuard();
        if ($user = $guard->user()) {
            if (empty($user->email_verified_at)) {
                $this->logout($guard);
                return true;
            }
        }

        return false;
    }

    public function refresh(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $refreshToken = new Token($request['refresh_token']);

            try {
                $payload = JWTAuth::setToken($refreshToken)->getPayload();
            } catch (\Exception $e) {
                throw new Exception('Token không hợp lệ', 401);
            }

            if ($payload['exp'] < Carbon::now()->timestamp) {
                throw new Exception('Refresh token đã hết hạn', 401);
            }

            if (!isset($payload['refresh']) || $payload['refresh'] !== true)
                throw new Exception('Token không hợp lệ', 401);

            $userId = $payload['sub'];
            $user = $this->userService->findById($userId);

            if (!$user)
                throw new Exception('User trong token không tồn tại', 404);

            $tokenJwtVersion = $payload->get('jwt_version', 0);
            if ($tokenJwtVersion != $user->jwt_version) {
                throw new Exception('Refresh token đã hết hạn, vui lòng đăng nhập lại', 401);
            }

            $this->blacklistCurrentToken($refreshToken);
            return $this->createTokenWithUserRecord($user);
        });
    }

    public function logout($guard = null)
    {
        return $this->tryThrow(function () use ($guard) {
            if (empty($guard))
                $guard = $this->getGuard();

            if ($user = $guard->user()) {
                $user->setRememberToken(null);
                $user->save();
            }

            $this->blacklistCurrentToken();

            try {
                $guard->logout();
            } catch (\Exception $e) {
                // Ignore logout errors (no session to logout)
            }
        });
    }

    public function blacklistCurrentToken($refreshToken = null)
    {
        try {
            $currentToken = JWTAuth::getToken();
            if ($currentToken) {
                JWTAuth::invalidate($currentToken);
            }
        } catch (\Exception $e) {
            // Không có token hoặc lỗi khác - bỏ qua
        }

        // Invalidate refresh token nếu có
        if ($refreshToken) {
            JWTAuth::invalidate($refreshToken);
        }
    }

    protected function createRefreshToken()
    {
        $user = auth('api')->user();
        $customClaims = [
            'refresh' => true,
            'jwt_version' => $user->jwt_version,
            'exp' => Carbon::now()->addMinutes(config('jwt.refresh_ttl'))->timestamp,
        ];

        return JWTAuth::customClaims($customClaims)->fromUser($user);
    }

    protected function createNewToken($token, $refreshToken)
    {
        $user = auth('api')->user();

        // Tạo lại access token với jwt_version
        $customClaims = ['jwt_version' => $user->jwt_version];
        $accessToken = JWTAuth::customClaims($customClaims)->fromUser($user);

        $user = $this->userService->formatRecord($user->toArray());
        $refreshPayload = JWTAuth::setToken($refreshToken)->getPayload();

        return [
            'token_type' => 'bearer',
            'access_token' => $accessToken,
            'access_token_expires_in' => config('jwt.ttl') * 60,
            'refresh_token' => $refreshToken,
            'refresh_token_expires_in' => max($refreshPayload['exp'] - Carbon::now()->timestamp, 0),
            'user' => $user,
        ];
    }

    public function logoutAllUsers(int $userId = null)
    {
        $this->incrementJwtVersion($userId);
        return ['message' => 'All users logged out successfully'];
    }

    public function incrementJwtVersion(int $userId = null)
    {
        return $this->tryThrow(function () use ($userId) {
            $this->userService->incrementJwtVersion($userId);
        }, true);
    }

    public function createTokenWithUserRecord($user)
    {
        $token = auth('api')->login($user);
        return $this->createNewToken($token, $this->createRefreshToken());
    }
}
