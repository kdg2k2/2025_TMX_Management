<?php

namespace App\Http\Requests\ContractPersonnel;

use App\Http\Requests\BaseRequest;

class SynctheticRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'personnel_id' => 'required|exists:personnels,id',
            'year' => 'nullable|exists:contracts,year',
            'investor_id' => 'nullable|exists:contract_investors,id',
            'contract_id' => 'nullable|exists:contracts,id',
        ];
    }
}
