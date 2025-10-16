<?php

namespace App\Http\Requests\ContractUnit;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'id' => app(FindByIdRequest::class)->rules()['id'],
                'name' => 'required|max:255|unique:contract_units,name,' . $this->id,
            ]
        );
    }
}
