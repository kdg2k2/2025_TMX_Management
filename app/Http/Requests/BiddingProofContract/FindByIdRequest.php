<?php

namespace App\Http\Requests\BiddingProofContract;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function prepareForValidation()
    {
        parent::prepareForValidation();
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:bidding_proof_contracts,id',
        ];
    }
}
