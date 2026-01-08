<?php

namespace App\Http\Requests\OfficialDocumentType;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:official_document_types,id'
        ];
    }
}
