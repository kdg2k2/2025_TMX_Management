<?php

namespace App\Http\Requests\BiddingProofContract;

use Illuminate\Foundation\Http\FormRequest;

class DeleteByProofContractIdRequest extends FindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:bidding_proof_contracts,proof_contract_id',
        ];
    }
}
