<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Exception;

trait TryCatchTraits
{
    /**
     * Execute callback and throw exceptions (sử dụng chung logic catchError)
     */
    public function tryThrow(callable $callback, $transaction = false)
    {
        $result = $this->catchError($callback, $transaction);

        if (!$result['success']) {
            // dd($result);
            // Recreate và throw lại exception dựa trên exception_type
            $this->throwException($result);
        }

        return $result['data'];
    }

    /**
     * Logic chung để catch errors và trả về structured result
     */
    private function catchError(callable $callback, $transaction = false)
    {
        try {
            $result = $transaction ? DB::transaction($callback) : $callback();
            return [
                'success' => true,
                'data' => $result
            ];
        } catch (TokenExpiredException $e) {
            return [
                'success' => false,
                'error_message' => 'Refresh token expired',
                'error_code' => $this->getErrorCode($e),
                'exception_type' => 'token_expired',
                'original_exception' => $e
            ];
        } catch (TokenInvalidException $e) {
            return [
                'success' => false,
                'error_message' => 'Invalid refresh token',
                'error_code' => $this->getErrorCode($e),
                'exception_type' => 'token_invalid',
                'original_exception' => $e
            ];
        } catch (ValidationException $e) {
            return [
                'success' => false,
                'error_message' => $e->errors(),
                'error_code' => $e->status,
                'exception_type' => 'validation',
                'original_exception' => $e
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error_message' => $e->getMessage(),
                'error_code' => $this->getErrorCode($e),
                'exception_type' => 'general',
                'original_exception' => $e
            ];
        }
    }

    /**
     * Xử lý cho API - trả về JSON response
     */
    public function catchAPI(callable $callback, $transaction = false)
    {
        $result = $this->catchError($callback, $transaction);

        if (!$result['success']) {
            return response()->json([
                'message' => $result['error_message'],
                'type' => $result['exception_type'] ?? 'error'
            ], $result['error_code']);
        }

        return $result['data'];
    }

    /**
     * Xử lý cho Web - redirect với error message
     */
    public function catchWeb(callable $callback, $transaction = false)
    {
        $result = $this->catchError($callback, $transaction);

        if (!$result['success']) {
            $errorMessage = is_array($result['error_message'])
                ? $this->formatValidationErrors($result['error_message'])
                : $result['error_message'];

            return redirect()->back()->with('error', $errorMessage);
        }

        return $result['data'];
    }

    /**
     * Xử lý cho cả API và Web với custom handler
     */
    public function catchCustom(callable $callback, callable $errorHandler, $transaction = false)
    {
        $result = $this->catchError($callback, $transaction);

        if (!$result['success']) {
            // Remove original_exception before passing to custom handler
            unset($result['original_exception']);
            return $errorHandler($result);
        }

        return $result['data'];
    }

    /**
     * Format validation errors thành string cho web
     */
    private function formatValidationErrors($errors)
    {
        if (!is_array($errors)) {
            return $errors;
        }

        $formatted = [];
        foreach ($errors as $field => $messages) {
            if (is_array($messages)) {
                $formatted[] = implode(', ', $messages);
            } else {
                $formatted[] = $messages;
            }
        }

        return implode('. ', $formatted);
    }

    /**
     * Throw lại exception dựa trên result từ catchError
     */
    private function throwException($result)
    {
        // Throw lại original exception để preserve stack trace
        if (isset($result['original_exception'])) {
            throw $result['original_exception'];
        }

        // Fallback nếu không có original exception
        switch ($result['exception_type']) {
            case 'token_expired':
                throw new TokenExpiredException('Refresh token expired');

            case 'token_invalid':
                throw new TokenInvalidException('Invalid refresh token');

            case 'validation':
                // Recreate ValidationException - cách đơn giản nhất
                $errors = is_array($result['error_message'])
                    ? $result['error_message']
                    : ['error' => [$result['error_message']]];

                throw ValidationException::withMessages($errors);

            case 'general':
            default:
                throw new Exception($result['error_message'], $result['error_code']);
        }
    }

    /**
     * Lấy error code hợp lệ
     */
    protected function getErrorCode($e)
    {
        $statusCode = $e->getCode() ?: 500;
        $statusCode = is_int($statusCode) && $statusCode >= 100 && $statusCode < 600
            ? $statusCode
            : 500;
        return $statusCode;
    }
}
