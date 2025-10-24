<?php

namespace App\Http\Requests\ContractType;

use App\Http\Requests\BaseRequest;

class FindByIdRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:contract_types,id',
        ];
    }
}
