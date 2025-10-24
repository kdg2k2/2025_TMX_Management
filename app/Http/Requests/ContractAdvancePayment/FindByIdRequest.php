<?php

namespace App\Http\Requests\ContractAdvancePayment;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:contract_advance_payments,id',
        ];
    }
}
