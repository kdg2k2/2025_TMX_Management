<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Closure;

class AuthAny
{
    /**
     * Handle an incoming request.
     *
     * Middleware này sẽ:
     * 1. Thử authenticate bằng JWT token trước (mobile)
     * 2. Kiểm tra JWT version nếu có JWT token
     * 3. Nếu không có JWT hoặc JWT invalid, thử web session
     * 4. Nếu cả 2 đều fail thì return 401
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authenticated = false;
        $user = null;

        // Bước 1: Thử JWT authentication (mobile)
        if ($request->hasHeader('Authorization')) {
            try {
                $user = JWTAuth::parseToken()->authenticate();
                if ($user) {
                    // Bước 1.1: Kiểm tra JWT version
                    if ($this->isJwtVersionValid($user)) {
                        auth()->setUser($user);
                        $authenticated = true;
                    } else {
                        // JWT version không khớp - tự logout và invalidate token
                        app(\App\Services\AuthService::class)->logout();

                        return response()->json([
                            'error' => 'Token version invalid',
                            'message' => 'Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại'
                        ], 401);
                    }
                }
            } catch (TokenExpiredException $e) {
                // Token expired - có thể trả về error để mobile refresh
                return response()->json([
                    'error' => 'Token expired',
                    'message' => 'Token đã hết hạn'
                ], 401);
            } catch (TokenInvalidException $e) {
                // Token invalid - thử fallback sang web session
            } catch (JWTException $e) {
                // Token not found hoặc malformed - thử fallback sang web session
            }
        }

        // Bước 2: Nếu JWT fail, thử web session authentication
        if (!$authenticated) {
            if (auth()->check()) {
                $authenticated = true;
                $user = auth()->user();
            }
        }

        // Bước 3: Nếu cả 2 đều fail
        if (!$authenticated) {
            if ($request->expectsJson() || $request->hasHeader('Authorization')) {
                // API request - trả về JSON
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Vui lòng đăng nhập'
                ], 401);
            } else {
                // Web request - redirect to login
                return redirect()->guest(route('login'));
            }
        }

        return $next($request);
    }

    /**
     * Kiểm tra JWT version có hợp lệ không
     */
    private function isJwtVersionValid($user): bool
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return false;
            }

            $payload = JWTAuth::setToken($token)->getPayload();
            $tokenJwtVersion = $payload->get('jwt_version', 0);

            // So sánh jwt_version trong token với jwt_version trong DB
            return $tokenJwtVersion == $user->jwt_version;
        } catch (\Exception $e) {
            // Nếu có lỗi khi lấy payload thì coi như invalid
            return false;
        }
    }
}
