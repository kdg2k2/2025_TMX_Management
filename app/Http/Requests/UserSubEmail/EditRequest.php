<?php

namespace App\Http\Requests\UserSubEmail;

class EditRequest extends FindByIdRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            app(ValidateUserIdRequest::class)->rules(),
        );
    }
}
