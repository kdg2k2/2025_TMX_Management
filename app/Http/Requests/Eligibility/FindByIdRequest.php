<?php

namespace App\Http\Requests\Eligibility;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:eligibilities,id',
        ];
    }
}
