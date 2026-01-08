<?php

namespace App\Http\Requests\OfficialDocumentType;

class UpdateRequest extends StoreRequest
{
    public function rules()
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
            [
                'name' => 'required|string|max:255|unique:official_document_types,name,' . $this->id,
            ]
        );
    }
}
