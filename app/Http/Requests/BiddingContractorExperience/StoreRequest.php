<?php

namespace App\Http\Requests\BiddingContractorExperience;

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
            'contract_id' => 'required|exists:contracts,id',
            'file_type' => 'required|in:path_file_full,path_file_short',
        ];
    }
}
