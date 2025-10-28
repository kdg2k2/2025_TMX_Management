<?php

namespace App\Http\Requests\UserSubEmail;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return array_merge(
            app(ValidateUserIdRequest::class)->rules(),
            [
                'email' => 'required|email'
            ]
        );
    }
}
