<?php

namespace App\Http\Requests\InternalBulletin;

use App\Http\Requests\BaseFindByIdRequest;

class FindByIdRequest extends BaseFindByIdRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|exists:internal_bulletins,id'
        ];
    }
}
