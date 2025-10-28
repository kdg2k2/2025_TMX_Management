<?php

namespace App\Http\Requests\UserSubEmail;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:user_sub_emails,id',
        ];
    }
}
