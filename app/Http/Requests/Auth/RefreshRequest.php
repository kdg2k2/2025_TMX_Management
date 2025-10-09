<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class RefreshRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'refresh_token' => 'required|string',
        ];
    }
}
