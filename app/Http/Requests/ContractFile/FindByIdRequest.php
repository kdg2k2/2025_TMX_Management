<?php

namespace App\Http\Requests\ContractFile;

use App\Http\Requests\BaseRequest;

class FindByIdRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'id' => $this->query('id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:contract_files,id',
        ];
    }
}
