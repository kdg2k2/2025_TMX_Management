<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class LoginRequest extends BaseRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'web_login' => $this->boolean('web_login'),
            'remember' => $this->boolean('remember'),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
            'web_login' => 'required|boolean',
            'remember' => 'nullable|boolean',
        ];
    }
}
