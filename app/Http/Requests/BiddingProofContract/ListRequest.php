<?php

namespace App\Http\Requests\BiddingProofContract;

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
