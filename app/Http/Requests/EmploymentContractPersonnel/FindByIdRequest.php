<?php

namespace App\Http\Requests\EmploymentContractPersonnel;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:employment_contract_personnels,id',
        ];
    }
}
