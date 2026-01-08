<?php

namespace App\Http\Requests\IncomingOfficialDocument;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:incoming_official_documents,id'
        ];
    }
}
