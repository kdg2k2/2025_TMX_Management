<?php

namespace App\Http\Requests\ContractInvestor;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:contract_investors,id',
        ];
    }
}
