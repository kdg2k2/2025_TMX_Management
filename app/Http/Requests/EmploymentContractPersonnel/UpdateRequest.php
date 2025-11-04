<?php

namespace App\Http\Requests\EmploymentContractPersonnel;


class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'id' => app(FindByIdRequest::class)->rules()['id'],
                'name' => 'required|max:255|unique:employment_contract_personnels,name,' . $this->id,
            ]
        );
    }
}
