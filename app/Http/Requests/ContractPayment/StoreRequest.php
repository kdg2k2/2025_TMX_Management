<?php

namespace App\Http\Requests\ContractPayment;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'contract_finance_id' => 'required|exists:contract_finances,id',
            'payment_amount' => 'required|integer|min:0',
            'invoice_amount' => 'required|integer|min:0',
            'payment_date' => 'required|date_format:Y-m-d',
            'invoice_date' => 'required|date_format:Y-m-d',
            'invoice_number' => 'required|string:max:255',
        ];
    }
}
