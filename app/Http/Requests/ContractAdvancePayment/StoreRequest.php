<?php

namespace App\Http\Requests\ContractAdvancePayment;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'contract_finance_id' => 'required|exists:contract_finances,id',
            'amount' => 'required|integer|min:0',
            'date' => 'required|date_format:Y-m-d',
        ];
    }
}
