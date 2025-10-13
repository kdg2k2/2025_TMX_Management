<?php

namespace App\Http\Requests\ContractInvestor;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'id' => app(FindByIdRequest::class)->rules()['id'],
            'name_vi' => 'required|string|max:255|unique:contract_investors,name_vi,' . $this->id,
            'name_en' => 'required|string|max:255|unique:contract_investors,name_en,' . $this->id,
        ]);
    }
}
