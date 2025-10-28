<?php

namespace App\Http\Requests\UserSubEmail;

use App\Http\Requests\BaseRequest;

class ValidateUserIdRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
        ];
    }
}
