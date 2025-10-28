<?php

namespace App\Http\Requests\UserSubEmail;

use App\Http\Requests\BaseRequest;

class IndexRequest extends BaseRequest
{
    public function rules(): array
    {
        return app(ValidateUserIdRequest::class)->rules();
    }
}
