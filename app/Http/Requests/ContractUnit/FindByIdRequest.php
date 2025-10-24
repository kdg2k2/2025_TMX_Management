<?php

namespace App\Http\Requests\ContractUnit;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:contract_units,id',
        ];
    }
}
