<?php

namespace App\Http\Requests\OfficialDocumentSector;

class UpdateRequest extends StoreRequest
{
    public function rules()
    {
        return array_merge(
            parent::rules(),
            app(FindByIdRequest::class)->rules(),
            [
                'name' => 'required|string|max:255|unique:official_document_sectors,name,' . $this->id,
            ]
        );
    }
}
