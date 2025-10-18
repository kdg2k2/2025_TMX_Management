<?php

namespace App\Http\Requests\ContractPayment;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'contract_finance_id' => 'nullable|exists:contract_finances,id',
        ]);
    }
}
