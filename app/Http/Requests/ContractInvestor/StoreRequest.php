<?php

namespace App\Http\Requests\ContractInvestor;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name_vi' => 'required|string|max:255|unique:contract_investors,name_vi',
            'name_en' => 'required|string|max:255|unique:contract_investors,name_en',
            'address' => 'nullable|string|max:255',
        ];
    }
}
