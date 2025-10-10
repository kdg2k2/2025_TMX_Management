<?php

namespace App\Http\Requests\ContractInvestor;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:contract_investors,name',
            'address' => 'nullable|string|max:255',
        ];
    }
}
