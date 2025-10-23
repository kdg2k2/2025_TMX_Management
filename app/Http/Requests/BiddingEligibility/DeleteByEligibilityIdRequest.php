<?php

namespace App\Http\Requests\BiddingEligibility;

use Illuminate\Foundation\Http\FormRequest;

class DeleteByEligibilityIdRequest extends FindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:bidding_eligibilities,eligibility_id',
        ];
    }
}
