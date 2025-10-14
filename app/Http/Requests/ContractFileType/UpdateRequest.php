<?php

namespace App\Http\Requests\ContractFileType;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            [
                'id' => app(FindByIdRequest::class)->rules()['id'],
                'name' => 'required|max:255|unique:contract_file_types,id,' . $this->id,
            ],
            parent::rules()
        );
    }
}
