<?php

namespace App\Http\Requests\Dossier;

use App\Http\Requests\BaseRequest;

class FindByContractIdAndYear extends BaseRequest
{
    public function rules()
    {
        return [
            'contract_id' => 'required|integer|exists:contracts,id',
            'nam' => 'nullable|integer|exists:contracts,nam'
        ];
    }
}
