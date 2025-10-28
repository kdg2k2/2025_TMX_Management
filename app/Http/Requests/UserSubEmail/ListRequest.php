<?php

namespace App\Http\Requests\UserSubEmail;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            app(ValidateUserIdRequest::class)->rules(),
        );
    }
}
