<?php

namespace App\Http\Requests\ContractBill;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:contract_bills,id',
        ];
    }
}
