<?php

namespace App\Http\Requests\BiddingContractorExperience;

class DeleteByContractIdRequest extends FindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:bidding_contractor_experiences,contract_id',
        ];
    }
}
