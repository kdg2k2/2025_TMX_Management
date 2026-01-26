<?php

namespace App\Http\Requests\ContractPersonnel;

use App\Http\Requests\BaseRequest;

class ExportRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'contract_id' => 'required|exists:contracts,id',
        ];
    }
}
