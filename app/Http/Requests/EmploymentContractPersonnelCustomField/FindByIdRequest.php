<?php

namespace App\Http\Requests\EmploymentContractPersonnelCustomField;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:employment_contract_personnel_custom_fields,id',
        ];
    }
}
