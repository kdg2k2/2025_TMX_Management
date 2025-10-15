<?php

namespace App\Http\Requests\ContractAppendix;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'contract_id' => 'nullable|exists:contracts,id',
        ]);
    }
}
