<?php

namespace App\Http\Requests\OfficialDocumentSector;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:official_document_sectors,id'
        ];
    }
}
