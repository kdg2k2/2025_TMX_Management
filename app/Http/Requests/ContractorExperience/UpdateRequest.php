<?php

namespace App\Http\Requests\ContractorExperience;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'id' => app(FindByIdRequest::class)->rules()['id'],
                'name' => 'required|max:255|unique:contractor_experiences,name,' . $this->id,
                'path' => 'nullable|file|mimes:pdf',
            ]
        );
    }
}
