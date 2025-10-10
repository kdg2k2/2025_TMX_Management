<?php

namespace App\Http\Requests\Contract;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'id' => app(FindByIdRequest::class)->rules()['id'],
            'short_name' => 'required|max:255|unique:contracts,short_name,' . $this->id,
            'contract_number' => 'required|max:255|unique:contracts,contract_number,' . $this->id,
        ]);
    }
}
