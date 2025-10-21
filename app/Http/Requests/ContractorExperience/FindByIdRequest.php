<?php

namespace App\Http\Requests\ContractorExperience;

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
            'id' => 'required|exists:contractor_experiences,id',
        ];
    }
}
