<?php

namespace App\Http\Requests\ContractFinance;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'contract_id' => 'required|exists:contracts,id',
            'contract_unit_id' => 'required|exists:contract_units,id',
            'role' => 'required|in:head_of_the_joint_venture,joint_venture_members,subcontractors',
            'realized_value' => 'required|integer|min:0',
            'acceptance_value' => 'required|integer|min:0',
            'vat_rate' => 'required|numeric|min:0',
            'vat_amount' => 'required|integer|min:0',
        ];
    }
}
