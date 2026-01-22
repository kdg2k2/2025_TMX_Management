<?php

namespace App\Http\Requests\ContractManyYear;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return [
            'contract_id' => 'nullable|exists:contracts,id'
        ];
    }
}
