<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class ForceLogoutUserRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->route('user_id') ?? null
        ]);
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
        ];
    }
}
