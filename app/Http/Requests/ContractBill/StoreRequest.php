<?php

namespace App\Http\Requests\ContractBill;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'created_by' => 'required|exists:users,id',
            'bill_collector' => 'required|exists:users,id',
            'contract_id' => 'required|exists:contracts,id',
            'path' => 'nullable|file|mimes:xls,xlsx,pdf,xml,jpg,rar,zip',
            'amount' => 'required|numeric|min:0',
            'duration' => 'required|date_format:Y-m-d',
            'content_in_the_estimate' => 'required|max:500',
            'note' => 'nullable|max:255',
        ];
    }
}
