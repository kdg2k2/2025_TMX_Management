<?php

namespace App\Http\Requests\ContractPersonnel;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:contract_personnels,id',
        ];
    }
}
