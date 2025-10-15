<?php

namespace App\Http\Requests\ContractFile;

use App\Http\Requests\BaseListRequest;

class ListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'contract_id' => 'nullable|exists:contract_bills,contract_id',
            'type_id' => 'nullable|exists:contract_file_types,id',
        ]);
    }
}
