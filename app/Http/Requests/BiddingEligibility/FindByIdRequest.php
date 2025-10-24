<?php

namespace App\Http\Requests\BiddingEligibility;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:bidding_eligibilities,id',
        ];
    }
}
