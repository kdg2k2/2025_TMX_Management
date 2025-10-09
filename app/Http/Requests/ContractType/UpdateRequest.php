<?php

namespace App\Http\Requests\ContractType;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'id' => app(FindByIdRequest::class)->rules()['id'],
            'name' => 'required|string|max:255|unique:contract_types,name,' . $this->id,
        ]);
    }
}
