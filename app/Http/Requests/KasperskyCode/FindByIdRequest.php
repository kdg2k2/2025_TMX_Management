<?php

namespace App\Http\Requests\KasperskyCode;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:kaspersky_codes,id',
        ];
    }
}
