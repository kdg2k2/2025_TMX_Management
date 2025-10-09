<?php

namespace App\Http\Requests\ContractType;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:contract_types,name',
            'description' => 'nullable|string|max:255',
        ];
    }
}
