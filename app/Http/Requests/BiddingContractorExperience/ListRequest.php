<?php

namespace App\Http\Requests\BiddingContractorExperience;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'bidding_id' => 'nullable|exists:biddings,id',
            ]
        );
    }
}
