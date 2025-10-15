<?php

namespace App\Http\Requests\ContractFileType;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function prepareForValidation()
    {
        $this->merge(parent::prepareForValidation());
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:contract_file_types,id',
        ];
    }
}
