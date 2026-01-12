<?php

namespace App\Http\Requests\KasperskyCodeRegistration;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:kaspersky_code_registrations,id'
        ];
    }
}
