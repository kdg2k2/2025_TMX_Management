<?php

namespace App\Http\Requests\BiddingContractorExperience;

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
            'id' => 'required|exists:bidding_contractor_experiences,id',
        ];
    }
}
