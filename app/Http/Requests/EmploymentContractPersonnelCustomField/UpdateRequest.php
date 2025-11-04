<?php

namespace App\Http\Requests\EmploymentContractPersonnelCustomField;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'id' => app(FindByIdRequest::class)->rules()['id'],
                'name' => 'required|max:255|unique:employment_contract_personnel_custom_fields,name,' . $this->id,
                'field' => 'required|max:255|unique:employment_contract_personnel_custom_fields,field,' . $this->id,
            ]
        );
    }
}
