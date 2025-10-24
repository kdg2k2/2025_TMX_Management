<?php

namespace App\Http\Requests\BiddingProofContract;

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
            'bidding_id' => 'required|exists:biddings,id',
            'proof_contract_id' => 'required|exists:proof_contracts,id',
        ];
    }
}
